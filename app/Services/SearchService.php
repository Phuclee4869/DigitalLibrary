<?php

namespace App\Services;

use App\Repositories\BookRepository;
use App\Repositories\BorrowRepository;
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

    public function searchBooks(array $filters): array
    {
        [$perPage, $clamped] = SearchSupport::resolvePerPage($filters['per_page'] ?? null);
        $page = max(1, (int) ($filters['page'] ?? 1));

        $result = $this->books->searchBooks($filters, $perPage, $page);

        return [
            'data' => $result->items(),
            'meta' => SearchSupport::meta($result, (int) ($filters['per_page'] ?? $perPage), $clamped),
        ];
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
