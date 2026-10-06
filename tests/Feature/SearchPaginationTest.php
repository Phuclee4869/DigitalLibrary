<?php

namespace Tests\Feature;

use App\Support\SearchSupport;
use Tests\TestCase;

/**
 * Kiểm thử đơn vị cho hàm hỗ trợ (không cần CSDL).
 * Chạy: php artisan test --filter=SearchPaginationTest
 */
class SearchPaginationTest extends TestCase
{
    public function test_per_page_bi_gioi_han_toi_da(): void
    {
        [$perPage, $clamped] = SearchSupport::resolvePerPage(100000);
        $this->assertSame(SearchSupport::MAX_PER_PAGE, $perPage);
        $this->assertTrue($clamped);
    }

    public function test_per_page_mac_dinh_va_toi_thieu(): void
    {
        $this->assertSame([10, false], SearchSupport::resolvePerPage(null));
        $this->assertSame([1, false], SearchSupport::resolvePerPage(0));
        $this->assertSame([25, false], SearchSupport::resolvePerPage('25'));
    }

    public function test_thoat_ky_tu_dai_dien_like(): void
    {
        $this->assertSame('100!% !_x !!', SearchSupport::escapeLike('100% _x !'));
    }

    public function test_tach_tu_khoa_va_gioi_han_so_tu(): void
    {
        $this->assertSame([], SearchSupport::splitTerms('   '));
        $this->assertSame(['lập', 'trình'], SearchSupport::splitTerms('  lập   trình '));
        $this->assertCount(SearchSupport::MAX_TERMS, SearchSupport::splitTerms('a b c d e f g'));
    }
}
