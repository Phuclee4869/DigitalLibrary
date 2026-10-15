<?php

namespace Tests\Feature;

use App\Support\CatalogCache;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * Buổi 9 - V3: kiểm thử bộ nhớ đệm danh mục sách (không cần CSDL; dùng kho đệm array).
 * Chạy: php artisan test --filter=CatalogCacheTest
 */
class CatalogCacheTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['cache.default' => 'array']);
        Cache::flush();
    }

    public function test_cach_tim_tuong_duong_dung_chung_khoa(): void
    {
        $a = CatalogCache::key(['keyword' => '  lập   trình ', 'per_page' => 100, 'order' => 'asc'], 1);
        $b = CatalogCache::key(['order' => 'asc', 'per_page' => 50, 'keyword' => 'lập trình', 'page' => 1], 1);

        $this->assertSame($a, $b, 'per_page > 50 bị cắt về 50, khoảng trắng thừa và thứ tự tham số không đổi kết quả');
    }

    public function test_bo_loc_khac_nhau_khong_dung_chung_khoa(): void
    {
        $base = CatalogCache::key(['keyword' => 'php'], 1);

        $this->assertNotSame($base, CatalogCache::key(['keyword' => 'php', 'page' => 2], 1));
        $this->assertNotSame($base, CatalogCache::key(['keyword' => 'php', 'category_id' => 3], 1));
        $this->assertNotSame($base, CatalogCache::key(['keyword' => 'php', 'available' => 'true'], 1));
        $this->assertNotSame($base, CatalogCache::key(['keyword' => 'php'], 2), 'đổi phiên bản là đổi khóa');
    }

    public function test_lan_hai_trung_dem_khong_goi_lai_truy_van(): void
    {
        $calls = 0;
        $producer = function () use (&$calls) {
            $calls++;
            return ['data' => [(object) ['id' => 1, 'ten_sach' => 'A']], 'meta' => ['total' => 1]];
        };

        [$first, $hit1]  = CatalogCache::remember(['keyword' => 'a'], $producer);
        [$second, $hit2] = CatalogCache::remember(['keyword' => 'a'], $producer);

        $this->assertFalse($hit1);
        $this->assertTrue($hit2);
        $this->assertSame(1, $calls);
        $this->assertSame($first, $second);
    }

    public function test_chi_luu_mang_thuan_khong_luu_doi_tuong(): void
    {
        [$result] = CatalogCache::remember([], fn () => ['data' => [(object) ['id' => 7]], 'meta' => []]);

        $this->assertIsArray($result['data'][0]);
        $this->assertSame(7, $result['data'][0]['id']);
    }

    public function test_vo_hieu_hoa_buoc_truy_van_lai(): void
    {
        $calls = 0;
        $producer = function () use (&$calls) {
            $calls++;
            return ['data' => [], 'meta' => ['lan' => $calls]];
        };

        CatalogCache::remember(['keyword' => 'x'], $producer);
        CatalogCache::invalidate();
        [$after, $hit] = CatalogCache::remember(['keyword' => 'x'], $producer);

        $this->assertFalse($hit, 'sau khi vô hiệu hóa phải là MISS');
        $this->assertSame(2, $calls);
        $this->assertSame(2, $after['meta']['lan']);
    }
}
