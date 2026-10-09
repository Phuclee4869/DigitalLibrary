<?php

namespace Tests\Feature;

use App\Services\ObjectAccessService as A;
use Tests\TestCase;

/**
 * Buổi 8 - V3: kiểm thử quy tắc quyết định kiểm quyền đối tượng (không cần CSDL).
 * Chạy: php artisan test --filter=ObjectAccessPolicyTest
 */
class ObjectAccessPolicyTest extends TestCase
{
    public function test_admin_va_thu_thu_thao_tac_moi_phieu(): void
    {
        foreach ([A::ROLE_ADMIN, A::ROLE_LIBRARIAN] as $role) {
            foreach ([A::ACTION_VIEW, A::ACTION_RENEW, A::ACTION_RETURN] as $action) {
                $this->assertTrue(A::allows($role, $action, false), "role $role, $action, không sở hữu");
                $this->assertTrue(A::allows($role, $action, true), "role $role, $action, sở hữu");
            }
        }
    }

    public function test_doc_gia_chi_xem_phieu_cua_minh(): void
    {
        $this->assertTrue(A::allows(A::ROLE_READER, A::ACTION_VIEW, true));
        $this->assertFalse(A::allows(A::ROLE_READER, A::ACTION_VIEW, false));
    }

    public function test_doc_gia_khong_duoc_tra_hoac_gia_han_ke_ca_phieu_cua_minh(): void
    {
        foreach ([A::ACTION_RENEW, A::ACTION_RETURN] as $action) {
            $this->assertFalse(A::allows(A::ROLE_READER, $action, true), $action);
            $this->assertFalse(A::allows(A::ROLE_READER, $action, false), $action);
        }
    }

    public function test_vai_tro_la_bi_tu_choi(): void
    {
        foreach ([0, 4, 99] as $role) {
            $this->assertFalse(A::allows($role, A::ACTION_VIEW, true), "role $role");
        }
    }

    public function test_phan_biet_nhan_vien(): void
    {
        $this->assertTrue(A::isStaff(1));
        $this->assertTrue(A::isStaff(2));
        $this->assertFalse(A::isStaff(3));
    }
}
