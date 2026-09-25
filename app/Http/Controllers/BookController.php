<?php

namespace App\Http\Controllers;

use App\Services\BookService;

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
}