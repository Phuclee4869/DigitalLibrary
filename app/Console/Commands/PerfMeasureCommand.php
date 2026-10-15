<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Support\CatalogCache;
use Illuminate\Cache\RateLimiter;
use Illuminate\Console\Command;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Throwable;

/**
 * Buổi 9 - V3: Đo hiệu năng các điểm cuối (yêu cầu tại chỗ số 1: mỗi điểm cuối đo 50 lần, lấy TRUNG VỊ).
 *
 *   php artisan perf:measure --label=truoc          (đo bản CHƯA tối ưu, lưu storage/app/perf/truoc.json)
 *   php artisan perf:measure --label=sau --compare=truoc   (đo bản đã tối ưu và in bảng so sánh)
 *   php artisan perf:measure --cold                 (xóa bộ nhớ đệm danh mục trước mỗi lần đo: đo đường MISS)
 *
 * Lệnh chỉ dùng bộ nhớ lõi của Laravel nên chép được vào bản mã nguồn CŨ để đo "trước".
 * Đo trong cùng tiến trình PHP (đi qua toàn bộ middleware, kiểm quyền, CSDL) nhưng KHÔNG gồm
 * độ trễ mạng và thời gian khởi động PHP: dùng để SO SÁNH trước/sau, không phải thời gian người dùng thấy.
 */
class PerfMeasureCommand extends Command
{
    protected $signature = 'perf:measure
        {--runs=50 : Số lần đo mỗi điểm cuối}
        {--warmup=3 : Số lần chạy khởi động, không tính}
        {--label= : Lưu kết quả vào storage/app/perf/<label>.json}
        {--compare= : So sánh với kết quả đã lưu <label>}
        {--cold : Xóa bộ nhớ đệm danh mục trước mỗi lần đo sách}
        {--skip-write : Bỏ qua hai điểm cuối ghi (lập phiếu, trả sách)}';

    protected $description = 'Đo hiệu năng điểm cuối: trung vị 50 lần, p95, số câu truy vấn (Buổi 9 - V3)';

    private int $queryCount = 0;
    private $limiterStore;

    public function handle(): int
    {
        $runs   = max(1, (int) $this->option('runs'));
        $warmup = max(0, (int) $this->option('warmup'));

        // Không để throttle (10/phút, 60/phút) chặn vòng đo: dùng kho đệm riêng và xóa trước mỗi lần gọi.
        $this->limiterStore = Cache::store('array');
        $store = $this->limiterStore;
        $this->laravel->singleton(RateLimiter::class, fn () => new RateLimiter($store));

        DB::listen(function () {
            $this->queryCount++;
        });

        $admin = User::where('email', 'admin@gmail.com')->first();
        if (!$admin) {
            $this->error('Không thấy admin@gmail.com. Chạy: php artisan db:seed --class=BorrowFlowSeeder --force');
            return self::FAILURE;
        }
        $token = $admin->createToken('perf-measure')->plainTextToken;

        try {
            $scenarios = $this->scenarios();
            $results = [];

            foreach ($scenarios as $name => $s) {
                if ($s['write'] && $this->option('skip-write')) {
                    continue;
                }
                $this->line("Đo: {$name} ...");
                $results[$name] = $s['write']
                    ? $this->measureWritePair($token, $runs, $warmup, $s)
                    : $this->measureRead($token, $runs, $warmup, $name, $s);
            }
        } catch (Throwable $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        } finally {
            $admin->tokens()->where('name', 'perf-measure')->delete();
        }

        $this->printTable($results, $runs);

        if ($label = $this->option('label')) {
            File::ensureDirectoryExists(storage_path('app/perf'));
            File::put(storage_path("app/perf/{$label}.json"), json_encode($results, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            $this->info("Đã lưu storage/app/perf/{$label}.json");
        }

        if ($cmp = $this->option('compare')) {
            $this->printComparison($results, $cmp);
        }

        return self::SUCCESS;
    }

    /** @return array<string,array> */
    private function scenarios(): array
    {
        $ticketId = DB::table('chi_tiet_phieu_muon')
            ->selectRaw('phieu_muon_id, COUNT(*) as c')
            ->groupBy('phieu_muon_id')
            ->orderByDesc('c')
            ->value('phieu_muon_id') ?? 1001;

        return [
            'GET /books (không lọc, 10 dòng)'           => ['method' => 'GET', 'uri' => '/api/v1/books', 'query' => ['per_page' => 10], 'cacheable' => true, 'write' => false],
            'GET /books?keyword=trình&sort=ten_sach'     => ['method' => 'GET', 'uri' => '/api/v1/books', 'query' => ['keyword' => 'trình', 'sort' => 'ten_sach', 'per_page' => 20], 'cacheable' => true, 'write' => false],
            'GET /borrow-tickets (20 dòng)'              => ['method' => 'GET', 'uri' => '/api/v1/borrow-tickets', 'query' => ['per_page' => 20], 'cacheable' => false, 'write' => false],
            'GET /borrow-tickets?trang_thai=đang mượn'   => ['method' => 'GET', 'uri' => '/api/v1/borrow-tickets', 'query' => ['trang_thai' => 'đang mượn', 'sort' => 'han_tra', 'order' => 'asc'], 'cacheable' => false, 'write' => false],
            'GET /activity-logs (20 dòng)'               => ['method' => 'GET', 'uri' => '/api/v1/activity-logs', 'query' => ['per_page' => 20], 'cacheable' => false, 'write' => false],
            "GET /borrow-tickets/{$ticketId}"            => ['method' => 'GET', 'uri' => "/api/v1/borrow-tickets/{$ticketId}", 'query' => [], 'cacheable' => false, 'write' => false],
            'POST /borrow-tickets (5 đầu sách)'          => ['method' => 'POST', 'write' => true, 'part' => 'create'],
            'POST /borrow-tickets/{id}/return (5 dòng)'  => ['method' => 'POST', 'write' => true, 'part' => 'return'],
        ];
    }

    private function measureRead(string $token, int $runs, int $warmup, string $name, array $s): array
    {
        $uri = $s['uri'] . ($s['query'] ? '?' . http_build_query($s['query']) : '');
        $times = [];
        $queries = [];
        $hits = 0;

        for ($i = 0; $i < $warmup + $runs; $i++) {
            if ($this->option('cold') && $s['cacheable'] && class_exists(CatalogCache::class)) {
                CatalogCache::invalidate();
            }

            [$status, $ms, $q, $xcache] = $this->call($s['method'], $uri, $token);
            if ($status !== 200) {
                throw new \RuntimeException("{$name}: mong đợi 200, nhận {$status}");
            }
            if ($i >= $warmup) {
                $times[] = $ms;
                $queries[] = $q;
                $hits += $xcache === 'HIT' ? 1 : 0;
            }
        }

        return $this->summarize($times, $queries, $s['cacheable'] ? $hits : null);
    }

    /**
     * Hai điểm cuối ghi không lặp lại được trên cùng dữ liệu, nên mỗi vòng: lập phiếu 5 đầu sách rồi trả ngay
     * (tồn kho trở về như cũ). Lập phiếu và trả sách được đo riêng; phần này chỉ chạy 1 lần cho cả hai dòng.
     */
    private function measureWritePair(string $token, int $runs, int $warmup, array $s): array
    {
        static $cache = null;

        if ($cache === null) {
            $readerId = $this->perfReaderId();
            $bookIds = DB::table('sach')->where('id', '<', 10000)->where('so_luong_con_lai', '>=', 2)
                ->orderBy('id')->limit(5)->pluck('id')->all();
            if (count($bookIds) < 5) {
                throw new \RuntimeException('Cần ít nhất 5 đầu sách còn >= 2 cuốn: php artisan db:seed --class=SearchDemoSeeder --force');
            }

            $body = [
                'doc_gia_id' => $readerId,
                'sach' => array_map(fn ($id) => ['sach_id' => $id, 'so_luong' => 1], $bookIds),
            ];

            $createTimes = $createQ = $returnTimes = $returnQ = [];

            for ($i = 0; $i < $warmup + $runs; $i++) {
                [$st, $ms, $q, , $content] = $this->call('POST', '/api/v1/borrow-tickets', $token, $body);
                if ($st !== 201) {
                    throw new \RuntimeException("POST /borrow-tickets: mong đợi 201, nhận {$st} - {$content}");
                }
                $ticketId = (int) json_decode($content, true)['data']['phieu_muon_id'];

                [$st2, $ms2, $q2, , $content2] = $this->call('POST', "/api/v1/borrow-tickets/{$ticketId}/return", $token);
                if ($st2 !== 200) {
                    throw new \RuntimeException("POST return: mong đợi 200, nhận {$st2} - {$content2}");
                }

                if ($i >= $warmup) {
                    $createTimes[] = $ms;  $createQ[] = $q;
                    $returnTimes[] = $ms2; $returnQ[]  = $q2;
                }
            }

            $cache = [
                'create' => $this->summarize($createTimes, $createQ, null),
                'return' => $this->summarize($returnTimes, $returnQ, null),
            ];
        }

        return $cache[$s['part']];
    }

    private function perfReaderId(): int
    {
        DB::table('doc_gia')->updateOrInsert(
            ['email' => 'perf.reader@example.com'],
            ['ho_ten' => 'Perf Reader', 'created_at' => now(), 'updated_at' => now()]
        );

        return (int) DB::table('doc_gia')->where('email', 'perf.reader@example.com')->value('id');
    }

    /** @return array{0:int,1:float,2:int,3:?string,4:string} */
    private function call(string $method, string $uri, string $token, array $json = []): array
    {
        Auth::forgetGuards();          // mỗi lần gọi nhận diện lại token như một yêu cầu mới
        $this->limiterStore->flush();  // không bị throttle

        $server = [
            'HTTP_ACCEPT'        => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
            'CONTENT_TYPE'       => 'application/json',
        ];
        $request = Request::create($uri, $method, [], [], [], $server, $json ? json_encode($json) : null);

        $kernel = $this->laravel->make(Kernel::class);

        $this->queryCount = 0;
        $t0 = hrtime(true);
        $response = $kernel->handle($request);
        $ms = (hrtime(true) - $t0) / 1e6;
        $q = $this->queryCount;

        $kernel->terminate($request, $response);

        return [$response->getStatusCode(), $ms, $q, $response->headers->get('X-Cache'), (string) $response->getContent()];
    }

    private function summarize(array $times, array $queries, ?int $hits): array
    {
        sort($times);
        sort($queries);
        $n = count($times);

        return [
            'runs'           => $n,
            'median_ms'      => round($this->median($times), 2),
            'p95_ms'         => round($times[max(0, (int) ceil(0.95 * $n) - 1)], 2),
            'min_ms'         => round($times[0], 2),
            'median_queries' => (int) $this->median($queries),
            'hit_rate'       => $hits === null ? null : round($hits / $n * 100),
        ];
    }

    private function median(array $sorted): float
    {
        $n = count($sorted);
        $mid = intdiv($n, 2);

        return $n % 2 ? (float) $sorted[$mid] : ($sorted[$mid - 1] + $sorted[$mid]) / 2;
    }

    private function printTable(array $results, int $runs): void
    {
        $rows = [];
        foreach ($results as $name => $r) {
            $rows[] = [$name, $r['median_ms'], $r['p95_ms'], $r['min_ms'], $r['median_queries'], $r['hit_rate'] === null ? '-' : $r['hit_rate'] . '%'];
        }
        $this->newLine();
        $this->info("Trung vị của {$runs} lần đo:");
        $this->table(['Điểm cuối', 'Trung vị (ms)', 'p95 (ms)', 'Min (ms)', 'Số truy vấn', 'Trúng đệm'], $rows);
    }

    private function printComparison(array $after, string $label): void
    {
        $path = storage_path("app/perf/{$label}.json");
        if (!File::exists($path)) {
            $this->warn("Không thấy {$path}");
            return;
        }
        $before = json_decode(File::get($path), true);

        $rows = [];
        foreach ($after as $name => $a) {
            $b = $before[$name] ?? null;
            if (!$b) {
                continue;
            }
            $gain = $b['median_ms'] > 0 ? round((1 - $a['median_ms'] / $b['median_ms']) * 100, 1) : 0;
            $rows[] = [$name, $b['median_ms'], $a['median_ms'], $gain . '%', $b['median_queries'], $a['median_queries']];
        }
        $this->newLine();
        $this->info("So sánh với '{$label}' (trung vị):");
        $this->table(['Điểm cuối', 'Trước (ms)', 'Sau (ms)', 'Giảm', 'Truy vấn trước', 'Truy vấn sau'], $rows);
    }
}
