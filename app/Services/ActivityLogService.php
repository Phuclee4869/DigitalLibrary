<?php

namespace App\Services;

use App\Repositories\ActivityLogRepository;
use App\Support\SearchSupport;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Buổi 7 - V3: Ghi và tra cứu nhật ký hoạt động hệ thống.
 */
class ActivityLogService
{
    /** Khóa metadata bị che trước khi lưu (không bao giờ ghi mật khẩu/token vào nhật ký). */
    private const SENSITIVE_KEYS = ['password', 'matkhau', 'token', 'secret', 'authorization'];

    public function __construct(protected ActivityLogRepository $logs) {}

    /**
     * Ghi một dòng nhật ký. Lỗi khi ghi nhật ký KHÔNG được làm hỏng nghiệp vụ chính.
     */
    public function record(
        string $action,
        ?string $entityType = null,
        ?int $entityId = null,
        ?string $description = null,
        array $metadata = [],
        ?int $userId = null
    ): void {
        try {
            $request = request();
            $userId ??= $request?->user()?->id;

            $this->logs->insert([
                'user_id'      => $userId,
                'hanh_dong'    => $action,
                'doi_tuong'    => $entityType,
                'doi_tuong_id' => $entityId,
                'mo_ta'        => $description !== null ? mb_substr($description, 0, 500) : null,
                'du_lieu'      => $metadata ? json_encode($this->mask($metadata), JSON_UNESCAPED_UNICODE) : null,
                'dia_chi_ip'   => $request?->ip(),
                'trinh_duyet'  => $request ? mb_substr((string) $request->userAgent(), 0, 255) : null,
                'created_at'   => now(),
            ]);
        } catch (Throwable $e) {
            Log::warning('Không ghi được nhật ký hệ thống: ' . $e->getMessage());
        }
    }

    public function search(array $filters): array
    {
        [$perPage, $clamped] = SearchSupport::resolvePerPage($filters['per_page'] ?? null);
        $page = max(1, (int) ($filters['page'] ?? 1));

        $result = $this->logs->search($filters, $perPage, $page);

        $rows = collect($result->items())->map(function ($r) {
            $r->du_lieu = $r->du_lieu ? json_decode($r->du_lieu, true) : null;
            return $r;
        })->all();

        return [
            'data' => $rows,
            'meta' => SearchSupport::meta($result, (int) ($filters['per_page'] ?? $perPage), $clamped),
        ];
    }

    private function mask(array $data): array
    {
        foreach ($data as $key => $value) {
            if (in_array(strtolower((string) $key), self::SENSITIVE_KEYS, true)) {
                $data[$key] = '***';
            } elseif (is_array($value)) {
                $data[$key] = $this->mask($value);
            }
        }
        return $data;
    }
}
