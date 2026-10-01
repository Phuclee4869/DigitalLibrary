<?php

namespace App\Http\Controllers;

use App\Services\BookService;
use Illuminate\Http\Request;

class BookController extends Controller
{
    protected BookService $bookService;

    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
    }

    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách sách thành công.',
            'data' => $this->bookService->getBooksList()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'author' => 'required|string',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thêm sách thành công.',
            'data' => $validated
        ], 201);
    }

    public function borrow(Request $request)
    {
        $validated = $request->validate([
            'doc_gia_id' => 'required|integer',
            'sach' => 'required|array|min:1',
            'sach.*.sach_id' => 'required|integer',
            'sach.*.so_luong' => 'required|integer|min:1',
        ]);

        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'error_code' => 'UNAUTHENTICATED',
                'message' => 'Chưa đăng nhập.'
            ], 401);
        }

        $ticketId = $this->bookService->createBorrowTicket(
            $user->id,
            $validated
        );

        return response()->json([
            'success' => true,
            'message' => 'Lập phiếu mượn thành công.',
            'data' => [
                'phieu_muon_id' => $ticketId
            ]
        ], 201);
    }
}