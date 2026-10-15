<?php

namespace App\Services;

use App\Repositories\BookRepository;
use App\Repositories\BorrowRepository;
use App\Support\CatalogCache;
use App\Support\SearchSupport;

/**
 * Buổi 7 - V3: Nghiệp vụ tìm kiếm, lọc, phân trang (sách và phiếu mượn).
 */
class SearchService
{
    public function __construct(
        protected BookRepository $books,
        protected BorrowRepository $borrows
    ) {}

    /**
     * Buổi 9: kết quả đi qua bộ nhớ đệm (CatalogCache). Khóa 'cache' = HIT | MISS để đo và gỡ lỗi.
     */
    public function searchBooks(array $filters): array
    {
        [$payload, $hit] = CatalogCache::remember($filters, function () use ($filters) {
            [$perPage, $clamped] = SearchSupport::resolvePerPage($filters['per_page'] ?? null);
            $page = max(1, (int) ($filters['page'] ?? 1));

            $result = $this->books->searchBooks($filters, $perPage, $page);

            return [
                'data' => $result->items(),
                'meta' => SearchSupport::meta($result, (int) ($filters['per_page'] ?? $perPage), $clamped),
            ];
        });

        // per_page quá 50 bị cắt về 50 nên dùng chung mục đệm; hai trường dưới đây phản ánh ĐÚNG yêu cầu hiện tại.
        [$effective, $clamped] = SearchSupport::resolvePerPage($filters['per_page'] ?? null);
        $payload['meta']['per_page_requested'] = (int) ($filters['per_page'] ?? $effective);
        $payload['meta']['da_gioi_han']        = $clamped;
        $payload['cache']                      = $hit ? 'HIT' : 'MISS';

        return $payload;
    }

    public function searchTickets(array $filters): array
    {
        [$perPage, $clamped] = SearchSupport::resolvePerPage($filters['per_page'] ?? null);
        $page = max(1, (int) ($filters['page'] ?? 1));

        $result = $this->borrows->searchTickets($filters, $perPage, $page);

        return [
            'data' => $result->items(),
            'meta' => SearchSupport::meta($result, (int) ($filters['per_page'] ?? $perPage), $clamped),
        ];
    }
}
