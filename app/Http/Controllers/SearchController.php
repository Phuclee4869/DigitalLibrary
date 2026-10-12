<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActivityLogSearchRequest;
use App\Http\Requests\BookSearchRequest;
use App\Http\Requests\BorrowTicketSearchRequest;
use App\Services\ActivityLogService;
use App\Services\SearchService;
use Illuminate\Http\JsonResponse;

class SearchController extends Controller
{
    public function __construct(
        protected SearchService $search,
        protected ActivityLogService $activityLogs
    ) {}

    /** GET /api/v1/books – Tìm kiếm, lọc, phân trang sách */
    public function books(BookSearchRequest $request): JsonResponse
    {
        $r = $this->search->searchBooks($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Tìm kiếm sách thành công.',
            'data'    => $r['data'],
            'meta'    => $r['meta'],
        ]);
    }

    /** GET /api/v1/borrow-tickets – Tìm kiếm, lọc, phân trang phiếu mượn (Admin, Thủ thư) */
    public function tickets(BorrowTicketSearchRequest $request): JsonResponse
    {
        $r = $this->search->searchTickets($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Tìm kiếm phiếu mượn thành công.',
            'data'    => $r['data'],
            'meta'    => $r['meta'],
        ]);
    }

    /** GET /api/v1/activity-logs – Tra cứu nhật ký hệ thống (chỉ Admin) */
    public function activityLogs(ActivityLogSearchRequest $request): JsonResponse
    {
        $r = $this->activityLogs->search($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Tra cứu nhật ký thành công.',
            'data'    => $r['data'],
            'meta'    => $r['meta'],
        ]);
    }
}
