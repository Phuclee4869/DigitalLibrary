<?php

namespace App\Http\Controllers;

use App\Http\Requests\BorrowTicketRequest;
use App\Services\BorrowService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BorrowController extends Controller
{
    public function __construct(protected BorrowService $borrowService) {}

    /**
     * POST /api/v1/borrow-tickets – Luồng 1: Lập phiếu mượn
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
     * GET /api/v1/borrow-tickets/{id} – Buổi 8: Xem chi tiết phiếu mượn (kiểm quyền đối tượng)
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $result = $this->borrowService->showTicket($id, $request->user());

        return response()->json([
            'success' => true,
            'message' => 'Lấy chi tiết phiếu mượn thành công.',
            'data'    => $result,
        ]);
    }

    /**
     * POST /api/v1/borrow-tickets/{id}/return – Luồng 2: Trả sách
     */
    public function returnBooks(Request $request, int $id): JsonResponse
    {
        $result = $this->borrowService->returnBorrowTicket($id, $request->user());

        return response()->json([
            'success' => true,
            'message' => 'Trả sách thành công.',
            'data'    => $result,
        ]);
    }

    /**
     * POST /api/v1/borrow-tickets/{id}/renew – Luồng 3: Gia hạn phiếu mượn
     */
    public function renew(Request $request, int $id): JsonResponse
    {
        $result = $this->borrowService->renewBorrowTicket($id, $request->user());

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'data'    => $result,
        ]);
    }
}
