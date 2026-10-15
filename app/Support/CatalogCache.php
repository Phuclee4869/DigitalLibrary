<?php

namespace App\Support;

use Closure;
use Illuminate\Support\Facades\Cache;
use Throwable;
use Illuminate\Support\Facades\Log;

/**
 * Buổi 9 - V3: Bộ nhớ đệm cho điểm cuối nặng nhất GET /api/v1/books.
 *
 * Nguyên tắc:
 *  1. Chỉ đệm dữ liệu CÔNG KHAI (danh sách sách). Không bao giờ đệm dữ liệu theo người dùng
 *     (chi tiết phiếu, nhật ký) để không làm hỏng kiểm quyền đối tượng của Buổi 8.
 *  2. Khóa đệm được chuẩn hóa từ bộ lọc hiệu lực: cùng một cách tìm viết khác thứ tự tham số,
 *     hoặc per_page quá 50 (bị cắt về 50), đều dùng chung một mục đệm.
 *  3. Vô hiệu hóa bằng "số phiên bản": tăng số này là toàn bộ mục cũ bị bỏ qua (không cần cache tag,
 *     chạy được trên mọi driver: database, file, redis). Mục cũ tự hết hạn theo TTL.
 *  4. Chỉ lưu mảng thuần (không lưu stdClass) để không phụ thuộc cấu hình serializable_classes.
 *  5. Lỗi của kho đệm KHÔNG được làm hỏng nghiệp vụ: lỗi thì bỏ qua đệm và truy vấn trực tiếp.
 */
class CatalogCache
{
    public const TTL_SECONDS = 120;

    private const VERSION_KEY = 'catalog:books:version';

    /**
     * Chuẩn hóa bộ lọc thành dạng hiệu lực (đầu vào của khóa đệm).
     */
    public static function normalize(array $f): array
    {
        [$perPage] = SearchSupport::resolvePerPage($f['per_page'] ?? null);

        $available = null;
        if (array_key_exists('available', $f) && $f['available'] !== null && $f['available'] !== '') {
            $available = filter_var($f['available'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
        }

        return [
            'keyword'     => implode(' ', SearchSupport::splitTerms($f['keyword'] ?? null)),
            'category_id' => !empty($f['category_id']) ? (int) $f['category_id'] : null,
            'available'   => $available,
            'sort'        => $f['sort'] ?? 'id',
            'order'       => ($f['order'] ?? 'asc') === 'desc' ? 'desc' : 'asc',
            'per_page'    => $perPage,
            'page'        => max(1, (int) ($f['page'] ?? 1)),
        ];
    }

    public static function key(array $filters, int $version): string
    {
        $hash = md5(json_encode(self::normalize($filters), JSON_UNESCAPED_UNICODE));

        return "catalog:books:v{$version}:{$hash}";
    }

    /**
     * Lấy kết quả từ đệm; chưa có thì gọi $producer, lưu lại rồi trả về.
     *
     * @return array{0: array, 1: bool}  [kết quả, có_trúng_đệm?]
     */
    public static function remember(array $filters, Closure $producer): array
    {
        try {
            $key    = self::key($filters, self::version());
            $cached = Cache::get($key);

            if (is_array($cached)) {
                return [$cached, true];
            }
        } catch (Throwable $e) {
            Log::warning('Bộ nhớ đệm danh mục sách không đọc được: ' . $e->getMessage());
            return [self::toPlainArray($producer()), false];
        }

        $fresh = self::toPlainArray($producer());

        try {
            Cache::put($key, $fresh, self::TTL_SECONDS);
        } catch (Throwable $e) {
            Log::warning('Bộ nhớ đệm danh mục sách không ghi được: ' . $e->getMessage());
        }

        return [$fresh, false];
    }

    /**
     * Gọi SAU KHI giao dịch làm thay đổi sách (tồn kho, thêm/sửa/xóa sách) đã commit.
     * Gọi trước commit thì người đọc có thể nạp lại bản cũ vào đệm.
     */
    public static function invalidate(): void
    {
        try {
            Cache::forever(self::VERSION_KEY, self::version() + 1);
        } catch (Throwable $e) {
            Log::warning('Không vô hiệu hóa được bộ nhớ đệm danh mục sách: ' . $e->getMessage());
        }
    }

    public static function version(): int
    {
        return max(1, (int) Cache::get(self::VERSION_KEY, 1));
    }

    private static function toPlainArray(array $payload): array
    {
        return json_decode(json_encode($payload), true);
    }
}
