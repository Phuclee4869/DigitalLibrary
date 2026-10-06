<?php

namespace App\Support;

use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Buổi 7 - V3: Hàm dùng chung cho tìm kiếm, lọc và phân trang.
 */
class SearchSupport
{
    public const DEFAULT_PER_PAGE = 10;
    public const MAX_PER_PAGE     = 50;   // Máy chủ tự giới hạn, không bao giờ trả quá 50 dòng/trang
    public const MAX_TERMS        = 5;    // Tối đa 5 từ khóa trong một lần tìm

    /**
     * Chuẩn hóa per_page: mặc định 10, tối thiểu 1, tối đa MAX_PER_PAGE.
     * Trả về [per_page_hiệu_lực, bị_giới_hạn?]
     */
    public static function resolvePerPage($requested): array
    {
        if ($requested === null || $requested === '') {
            return [self::DEFAULT_PER_PAGE, false];
        }

        $requested = max(1, (int) $requested);
        $effective = min($requested, self::MAX_PER_PAGE);

        return [$effective, $requested > self::MAX_PER_PAGE];
    }

    /**
     * Tách chuỗi tìm kiếm thành các từ khóa (AND), bỏ khoảng trắng thừa.
     */
    public static function splitTerms(?string $keyword): array
    {
        $keyword = trim((string) $keyword);
        if ($keyword === '') {
            return [];
        }

        $terms = preg_split('/\s+/u', $keyword, -1, PREG_SPLIT_NO_EMPTY);

        return array_slice($terms, 0, self::MAX_TERMS);
    }

    /**
     * Thoát ký tự đại diện của LIKE (% _) để người dùng không tự chèn mẫu.
     * Dùng '!' làm ký tự thoát: chạy giống nhau trên MySQL và SQLite.
     * Câu SQL đi kèm: "cot LIKE ? ESCAPE '!'"
     */
    public static function escapeLike(string $value): string
    {
        return str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $value);
    }

    /**
     * Chuyển đối tượng phân trang của Laravel thành khối meta gọn, ổn định.
     */
    public static function meta(LengthAwarePaginator $page, int $requestedPerPage, bool $clamped): array
    {
        return [
            'current_page'      => $page->currentPage(),
            'per_page'          => $page->perPage(),
            'per_page_requested'=> $requestedPerPage,
            'per_page_max'      => self::MAX_PER_PAGE,
            'da_gioi_han'       => $clamped,
            'total'             => $page->total(),
            'last_page'         => $page->lastPage(),
            'from'              => $page->firstItem(),
            'to'                => $page->lastItem(),
            'has_more'          => $page->hasMorePages(),
        ];
    }
}
