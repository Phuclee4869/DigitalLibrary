<?php

namespace App\Http\Controllers;

use App\Services\ObjectAccessService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(protected ObjectAccessService $access) {}

    /**
     * DELETE /api/v1/users/{id} – chỉ Admin (role:1) + Buổi 8: kiểm quyền trên tài khoản đích.
     * Giữ nguyên hành vi mô phỏng của nhóm (chưa xóa thật bản ghi).
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $this->access->authorizeUserDeletion($request->user(), $id);

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa người dùng',
            'user_id' => $id,
        ]);
    }
}
