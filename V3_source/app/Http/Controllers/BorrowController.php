<?php

namespace App\Http\Controllers;

use App\Http\Requests\BorrowTicketRequest;
use App\Services\BorrowService;
use Illuminate\Http\JsonResponse;

/**
 * Tầng giao diện (API): chỉ nhận yêu cầu, gọi service và trả JSON.
 * Không chứa quy tắc nghiệp vụ.
 */
class BorrowController extends Controller
{
    public function __construct(protected BorrowService $borrowService)
    {
    }

    /**
     * POST /api/v1/borrow-tickets — Lập phiếu mượn
     */
    public function store(BorrowTicketRequest $request): JsonResponse
    {
        $ticket = $this->borrowService->createBorrowTicket(
            $request->user()->id,
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Lập phiếu mượn thành công.',
            'data'    => $ticket,
        ], 201);
    }

    /**
     * POST /api/v1/borrow-tickets/{id}/return — Trả sách
     */
    public function returnBooks(int $id): JsonResponse
    {
        $result = $this->borrowService->returnBorrowTicket($id);

        return response()->json([
            'success' => true,
            'message' => 'Trả sách thành công.',
            'data'    => $result,
        ]);
    }
}
