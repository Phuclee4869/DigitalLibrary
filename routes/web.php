<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SachController;
use App\Http\Controllers\DanhMucController;
use App\Http\Controllers\TacGiaController;
use App\Http\Controllers\NhaXuatBanController;
use App\Http\Controllers\DocGiaController;
use App\Http\Controllers\PhieuMuonController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\ThongKeController;
use Illuminate\Support\Facades\DB;

// Các route không cần xác thực
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [HomeController::class, 'search'])->name('search');
Route::get('/danh-muc/{id}', [HomeController::class, 'filterByCategory'])->name('danh-muc.filter');
Route::get('/sach/{id}', [HomeController::class, 'show'])->name('sach.detail');

// Routes xác thực
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Quên mật khẩu
Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

// Nhóm các route cần xác thực
Route::middleware(['auth'])->group(function () {
    // Route cho Admin Dashboard
    Route::get('/admin', [AdminController::class, 'index'])->middleware('admin.or.thuthu')->name('admin.dashboard');

    // Sử dụng group để áp dụng middleware và prefix cho các route admin
    Route::group(['prefix' => 'admin', 'middleware' => 'admin.or.thuthu'], function () {
        // Route cho Sách - định nghĩa từng route một để kiểm soát tốt hơn
        Route::get('/sach', [SachController::class, 'index'])->name('sach.index');
        Route::get('/sach/create', [SachController::class, 'create'])->name('sach.create');
        Route::post('/sach', [SachController::class, 'store'])->name('sach.store');
        Route::get('/sach/{sach}/edit', [SachController::class, 'edit'])->name('sach.edit');
        Route::put('/sach/{sach}', [SachController::class, 'update'])->name('sach.update');
        Route::delete('/sach/{sach}', [SachController::class, 'destroy'])->name('sach.destroy');


        // Route cho độc giả
        Route::put('/doc-gia/{docGia}', [DocGiaController::class, 'update'])->name('doc-gia.update');
        Route::get('/doc-gia/{docGia}/edit', [DocGiaController::class, 'edit'])->name('doc-gia.edit');
        Route::delete('/doc-gia/{docGia}', [DocGiaController::class, 'destroy'])->name('doc-gia.destroy');
        Route::get('/doc-gia/{docGia}', [DocGiaController::class, 'show'])->name('doc-gia.show');
        Route::post('/doc-gia', [DocGiaController::class, 'store'])->name('doc-gia.store');
        Route::get('/doc-gia/create', [DocGiaController::class, 'create'])->name('doc-gia.create');
        Route::get('/doc-gia', [DocGiaController::class, 'index'])->name('doc-gia.index');


        //route cho tác giả
        Route::get('/tac-gia', [TacGiaController::class, 'index'])->name('tac-gia.index');
        Route::get('/tac-gia/create', [TacGiaController::class, 'create'])->name('tac-gia.create');
        Route::post('/tac-gia', [TacGiaController::class, 'store'])->name('tac-gia.store');
        Route::get('/tac-gia/{tacGia}/edit', [TacGiaController::class, 'edit'])->name('tac-gia.edit');
        Route::put('/tac-gia/{tacGia}', [TacGiaController::class, 'update'])->name('tac-gia.update');
        Route::delete('/tac-gia/{tacGia}', [TacGiaController::class, 'destroy'])->name('tac-gia.destroy');



        // Route cho Danh mục - định nghĩa từng route một
        Route::get('/danh-muc', [DanhMucController::class, 'index'])->name('danh-muc.index');
        Route::get('/danh-muc/create', [DanhMucController::class, 'create'])->name('danh-muc.create');
        Route::post('/danh-muc', [DanhMucController::class, 'store'])->name('danh-muc.store');
        Route::get('/danh-muc/{danhMuc}/edit', [DanhMucController::class, 'edit'])->name('danh-muc.edit');
        Route::put('/danh-muc/{danhMuc}', [DanhMucController::class, 'update'])->name('danh-muc.update');
        Route::delete('/danh-muc/{danhMuc}', [DanhMucController::class, 'destroy'])->name('danh-muc.destroy');

        // Sử dụng resource cho các controller khác
        Route::resource('tac-gia', TacGiaController::class);
        Route::resource('nha-xuat-ban', NhaXuatBanController::class);
        Route::resource('doc-gia', DocGiaController::class);

        // Route cho Phiếu mượn
        Route::resource('phieu-muon', PhieuMuonController::class);
        Route::get('phieu-muon/{phieuMuon}/return', [PhieuMuonController::class, 'returnBook'])->name('phieu-muon.return');
        Route::post('phieu-muon/{phieuMuon}/process-return', [PhieuMuonController::class, 'processReturn'])->name('phieu-muon.process-return');
    });

    // Thêm routes cho thống kê
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/thong-ke', [ThongKeController::class, 'index'])->name('thong-ke.index');
        Route::get('/thong-ke/muon-thang', [ThongKeController::class, 'thongKeMuonThang'])
            ->name('thong-ke.muon-thang');
        Route::get('/thong-ke/xuat-pdf', [ThongKeController::class, 'xuatPDF'])
            ->name('thong-ke.xuat-pdf');
    });


    // Route chỉ dành cho Admin
    Route::middleware(['admin'])->group(function () {
        Route::resource('user', UserController::class);
    });
});

// Sửa lại route debug-app (giữ nguyên)
Route::get('/debug-app', function () {
    // Code giữ nguyên
    $dbStatus = null;
    $tables = [];
    $danhMucCount = 0;
    $danhMucs = [];
    $sachCount = 0;
    $sachs = [];
    $relationshipCheck = [];

    // Kiểm tra kết nối database
    try {
        $dbStatus = DB::connection()->getPdo() ? "Kết nối thành công: " . DB::connection()->getDatabaseName() : "Không kết nối được";
    } catch (\Exception $e) {
        $dbStatus = "Lỗi kết nối: " . $e->getMessage();
    }

    // Kiểm tra các bảng
    try {
        $result = DB::select('SHOW TABLES');
        foreach ($result as $row) {
            $tables[] = get_object_vars($row)[key(get_object_vars($row))];
        }
    } catch (\Exception $e) {
        $tables = ["Lỗi: " . $e->getMessage()];
    }

    // Kiểm tra số lượng danh mục
    try {
        $danhMucCount = \App\Models\DanhMuc::count();
        $danhMucs = \App\Models\DanhMuc::all();
    } catch (\Exception $e) {
        $danhMucCount = "Lỗi: " . $e->getMessage();
        $danhMucs = [];
    }

    // Kiểm tra số lượng sách
    try {
        $sachCount = \App\Models\Sach::count();
        $sachs = \App\Models\Sach::take(5)->get();
    } catch (\Exception $e) {
        $sachCount = "Lỗi: " . $e->getMessage();
        $sachs = [];
    }

    // Kiểm tra relationship
    if ($sachs && count($sachs) > 0) {
        foreach ($sachs as $sach) {
            try {
                $danhMuc = $sach->danhMuc;
                $relationshipCheck[] = "Sách ID {$sach->id} - Danh mục: " . ($danhMuc ? $danhMuc->ten_danh_muc : "Không có");
            } catch (\Exception $e) {
                $relationshipCheck[] = "Lỗi với sách ID {$sach->id}: " . $e->getMessage();
            }
        }
    }

    return [
        'php_version' => phpversion(),
        'laravel_version' => app()->version(),
        'database_connection' => $dbStatus,
        'tables' => $tables,
        'danh_muc_count' => $danhMucCount,
        'danh_mucs_sample' => $danhMucs,
        'sach_count' => $sachCount,
        'sachs_sample' => $sachs,
        'relationship_check' => $relationshipCheck
    ];
});
