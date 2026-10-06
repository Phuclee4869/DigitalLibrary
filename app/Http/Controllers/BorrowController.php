<?php

namespace App\Http\Controllers;

use App\Http\Requests\BorrowTicketRequest;
use App\Services\BorrowService;
use Illuminate\Http\JsonResponse;

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
     * POST /api/v1/borrow-tickets/{id}/return – Luồng 2: Trả sách
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

    /**
     * POST /api/v1/borrow-tickets/{id}/renew – Luồng 3: Gia hạn phiếu mượn
     */
    public function renew(int $id): JsonResponse
    {
        $result = $this->borrowService->renewBorrowTicket($id);

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'data'    => $result,
        ]);
    }
}
