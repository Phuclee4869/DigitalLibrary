<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use App\Repositories\BorrowRepository;
use App\Repositories\UserRepository;

/**
 * Buổi 8 - V3: Kiểm quyền trên đối tượng (object-level authorization).
 *
 * Middleware role:x,y chỉ trả lời "vai trò này có được dùng chức năng không?".
 * Lớp này trả lời thêm câu hỏi: "người này có được thao tác trên ĐÚNG đối tượng có mã {id} không?"
 * nhằm chặn việc đổi {id} trên đường dẫn để truy cập dữ liệu của người khác (IDOR/BOLA).
 */
class ObjectAccessService
{
    public const ROLE_ADMIN     = 1;
    public const ROLE_LIBRARIAN = 2;
    public const ROLE_READER    = 3;

    public const ACTION_VIEW   = 'view';
    public const ACTION_RENEW  = 'renew';
    public const ACTION_RETURN = 'return';

    /** Hành động Độc giả được làm trên phiếu CỦA CHÍNH MÌNH (mở rộng ở đây nếu cho phép tự gia hạn). */
    public const READER_OWN_ACTIONS = [self::ACTION_VIEW];

    private const ACTION_LABELS = [
        self::ACTION_VIEW   => 'xem',
        self::ACTION_RENEW  => 'gia hạn',
        self::ACTION_RETURN => 'trả',
    ];

    public function __construct(
        protected BorrowRepository $borrows,
        protected UserRepository $users
    ) {}

    /**
     * Quy tắc quyết định thuần (không cần CSDL) - dùng cho cả kiểm thử đơn vị.
     *  - Admin, Thủ thư: mọi phiếu.
     *  - Độc giả: chỉ phiếu của mình và chỉ các hành động trong READER_OWN_ACTIONS.
     *  - Vai trò lạ / không xác định: từ chối.
     */
    public static function allows(int $roleId, string $action, bool $isOwner): bool
    {
        if (self::isStaff($roleId)) {
            return true;
        }

        if ($roleId === self::ROLE_READER) {
            return $isOwner && in_array($action, self::READER_OWN_ACTIONS, true);
        }

        return false;
    }

    public static function isStaff(int $roleId): bool
    {
        return in_array($roleId, [self::ROLE_ADMIN, self::ROLE_LIBRARIAN], true);
    }

    /**
     * Kiểm quyền thao tác trên một phiếu mượn. Trả về dòng phiếu (kèm chu_so_huu_user_id) nếu hợp lệ.
     *
     * Chống dò mã (enumeration): với Độc giả, phiếu "không tồn tại" và phiếu "của người khác"
     * trả về CÙNG một phản hồi 403 OBJECT_FORBIDDEN; chỉ nhân viên mới nhận 404 TICKET_NOT_FOUND.
     */
    public function authorizeTicket(object $actor, int $ticketId, string $action): object
    {
        $role   = (int) $actor->role_id;
        $ticket = $this->borrows->findTicketWithOwner($ticketId);

        if (!$ticket) {
            if (self::isStaff($role)) {
                throw new BusinessException('Không tìm thấy phiếu mượn.', 'TICKET_NOT_FOUND', 404);
            }
            throw $this->denied($action);
        }

        $isOwner = $ticket->chu_so_huu_user_id !== null
            && (int) $ticket->chu_so_huu_user_id === (int) $actor->id;

        if (!self::allows($role, $action, $isOwner)) {
            throw $this->denied($action);
        }

        return $ticket;
    }

    /**
     * Kiểm quyền trên tài khoản bị xóa: phải tồn tại và Admin không được tự xóa chính mình
     * (tránh mất quyền quản trị của hệ thống).
     */
    public function authorizeUserDeletion(object $actor, int $targetUserId): object
    {
        $target = $this->users->findById($targetUserId);

        if (!$target) {
            throw new BusinessException('Không tìm thấy người dùng.', 'USER_NOT_FOUND', 404);
        }

        if ((int) $target->id === (int) $actor->id) {
            throw new BusinessException(
                'Quản trị viên không thể tự xóa tài khoản của chính mình.',
                'SELF_DELETE_FORBIDDEN',
                403
            );
        }

        return $target;
    }

    private function denied(string $action): BusinessException
    {
        return new BusinessException(
            'Bạn không có quyền ' . (self::ACTION_LABELS[$action] ?? 'thao tác trên') . ' phiếu mượn này.',
            'OBJECT_FORBIDDEN',
            403,
            ['hanh_dong' => $action]
        );
    }
}
