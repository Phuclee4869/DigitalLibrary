# BUOI 10 - V3: phep thu dung lai tren may sach (Windows PowerShell 5.1+)
# Chay tai thu muc goc du an (noi co artisan, composer.json):
#   powershell -ExecutionPolicy Bypass -File .\rebuild_check.ps1
# Ket qua: man hinh + tep rebuild_log.txt (chup man hinh / dinh kem vao bao cao).
# Chi dung lenh trong HUONG_DAN_CAI_DAT.txt; script chi tu dong hoa va do thoi gian.

$ErrorActionPreference = 'Continue'
$port = 8000
$base = "http://127.0.0.1:$port/api/v1"
$logFile = Join-Path (Get-Location) 'rebuild_log.txt'
$results = New-Object System.Collections.ArrayList
$total = [System.Diagnostics.Stopwatch]::StartNew()
"REBUILD LOG - $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss') - $env:COMPUTERNAME" | Out-File $logFile -Encoding utf8

function Log($m) { Write-Host $m; $m | Out-File $logFile -Append -Encoding utf8 }

function Step($id, $name, [scriptblock]$body) {
    $sw = [System.Diagnostics.Stopwatch]::StartNew()
    Log ""
    Log "=== [$id] $name"
    $ok = $false; $note = ''
    try { $r = & $body; $ok = [bool]$r.ok; $note = [string]$r.note } catch { $note = $_.Exception.Message }
    $sw.Stop()
    $status = if ($ok) { 'DAT' } else { 'LOI' }
    Log ("    -> {0} ({1:N1} giay) {2}" -f $status, $sw.Elapsed.TotalSeconds, $note)
    [void]$results.Add([pscustomobject]@{ Buoc = $id; Ten = $name; KetQua = $status; Giay = [math]::Round($sw.Elapsed.TotalSeconds, 1); GhiChu = $note })
}

function Run($cmd) {
    Log "    > $cmd"
    $out = cmd /c "$cmd 2>&1"
    $code = $LASTEXITCODE
    ($out | Select-Object -Last 12) | ForEach-Object { Log "      $_" }
    return @{ code = $code; out = ($out -join "`n") }
}

function Api($method, $path, $token, $body) {
    $h = @{ Accept = 'application/json' }
    if ($token) { $h.Authorization = "Bearer $token" }
    try {
        $p = @{ Uri = "$base$path"; Method = $method; Headers = $h; UseBasicParsing = $true }
        if ($body) { $p.Body = ($body | ConvertTo-Json -Depth 6); $p.ContentType = 'application/json' }
        $r = Invoke-WebRequest @p
        return @{ status = [int]$r.StatusCode; json = ($r.Content | ConvertFrom-Json); headers = $r.Headers }
    } catch {
        $resp = $_.Exception.Response
        if ($resp) { return @{ status = [int]$resp.StatusCode; json = $null; headers = $null } }
        return @{ status = 0; json = $null; headers = $null }
    }
}

# B1. Danh muc tep
Step 'B1' 'Danh muc tep day du' {
    $need = 'artisan','composer.json','composer.lock','.env.example','routes/api.php','bootstrap/app.php',
            'app/Services/BorrowService.php','app/Repositories/BorrowRepository.php','database/migrations','database/seeders/BorrowFlowSeeder.php'
    $miss = $need | Where-Object { -not (Test-Path $_) }
    @{ ok = ($miss.Count -eq 0); note = $(if ($miss) { 'THIEU: ' + ($miss -join ', ') } else { 'du ' + $need.Count + ' muc then chot' }) }
}

# B2. Moi truong
Step 'B2' 'PHP, Composer, extension SQLite' {
    $v = Run 'php -v'; $c = Run 'composer -V'; $m = Run 'php -m'
    $hasSqlite = $m.out -match 'pdo_sqlite'
    @{ ok = ($v.code -eq 0 -and $c.code -eq 0 -and $hasSqlite); note = $(if (-not $hasSqlite) { 'thieu pdo_sqlite trong php.ini' } else { '' }) }
}

# B3. Cai thu vien
Step 'B3' 'composer install' { $r = Run 'composer install --no-interaction'; @{ ok = ($r.code -eq 0); note = "exit $($r.code)" } }

# B4. Cau hinh
Step 'B4' 'Tao .env va APP_KEY' {
    if (-not (Test-Path '.env')) { Copy-Item '.env.example' '.env' }
    $r = Run 'php artisan key:generate --force'
    $key = (Select-String -Path '.env' -Pattern '^APP_KEY=base64:' -Quiet)
    @{ ok = ($r.code -eq 0 -and $key); note = $(if (-not $key) { 'APP_KEY rong' } else { '' }) }
}

# B5. CSDL
Step 'B5' 'Tao tep SQLite va migrate' {
    if (-not (Test-Path 'database\database.sqlite')) { New-Item -ItemType File 'database\database.sqlite' | Out-Null }
    $r = Run 'php artisan migrate --force'
    $tok = Run 'php artisan migrate:status'
    $hasTok = $tok.out -match 'personal_access_tokens'
    @{ ok = ($r.code -eq 0 -and $hasTok); note = $(if (-not $hasTok) { 'THIEU migration personal_access_tokens (login se loi 500)' } else { '' }) }
}

# B6. Du lieu mau
Step 'B6' 'Nap du lieu mau (BorrowFlowSeeder, SearchDemoSeeder)' {
    $a = Run 'php artisan db:seed --class=BorrowFlowSeeder --force'
    $b = Run 'php artisan db:seed --class=SearchDemoSeeder --force'
    $users = Run 'php artisan tinker --execute="echo App\Models\User::count();"'
    @{ ok = ($a.code -eq 0 -and $b.code -eq 0); note = "BorrowFlow exit $($a.code); SearchDemo exit $($b.code)" }
}

# B7. Kiem thu tu dong
Step 'B7' 'php artisan test (CatalogCacheTest, SearchPaginationTest)' {
    $r = Run 'php artisan test --filter="CatalogCacheTest|SearchPaginationTest"'
    @{ ok = ($r.code -eq 0); note = "exit $($r.code)" }
}

# B8. Chay may chu
$server = $null
Step 'B8' "Khoi dong php artisan serve (cong $port)" {
    $script:server = Start-Process -FilePath 'php' -ArgumentList 'artisan','serve',"--port=$port" -PassThru -WindowStyle Hidden
    $up = $false
    1..20 | ForEach-Object { if (-not $up) { Start-Sleep -Milliseconds 500; $x = Api 'GET' '/books' $null $null; if ($x.status -eq 200) { $up = $true } } }
    @{ ok = $up; note = $(if ($up) { 'GET /books = 200' } else { 'may chu khong tra loi' }) }
}

# B9. Dang nhap lai toan bo tai khoan (yeu cau tai cho 3)
$tokens = @{}
Step 'B9' 'Dang nhap 3 tai khoan kiem thu' {
    $okAll = $true; $note = @()
    foreach ($e in 'admin@gmail.com','thuthu@gmail.com','docgia@gmail.com') {
        $r = Api 'POST' '/login' $null @{ email = $e; password = '123456' }
        if ($r.status -eq 200 -and $r.json.token) { $script:tokens[$e] = $r.json.token; $note += "$e=200" } else { $okAll = $false; $note += "$e=$($r.status)" }
    }
    @{ ok = $okAll; note = ($note -join '; ') }
}

# B10. Luong nghiep vu va phan quyen
Step 'B10' 'Kiem tra nhanh: 401, 403, lap phieu, tra sach, X-Cache' {
    $notes = @(); $ok = $true
    $r = Api 'POST' '/borrow-tickets' $null @{ doc_gia_id = 1; sach = @(@{ sach_id = 1; so_luong = 1 }) }
    $notes += "khong token=$($r.status)(mong 401)"; if ($r.status -ne 401) { $ok = $false }
    $r = Api 'POST' '/borrow-tickets' $tokens['docgia@gmail.com'] @{ doc_gia_id = 1; sach = @(@{ sach_id = 1; so_luong = 1 }) }
    $notes += "doc gia=$($r.status)(mong 403)"; if ($r.status -ne 403) { $ok = $false }
    $r = Api 'POST' '/borrow-tickets' $tokens['thuthu@gmail.com'] @{ doc_gia_id = 1; sach = @(@{ sach_id = 1; so_luong = 1 }) }
    $notes += "lap phieu=$($r.status)(mong 201)"; if ($r.status -ne 201) { $ok = $false }
    $tid = $null; if ($r.json) { $tid = $r.json.data.phieu_muon_id }
    if ($tid) {
        $r = Api 'POST' "/borrow-tickets/$tid/return" $tokens['thuthu@gmail.com'] $null
        $notes += "tra sach=$($r.status)(mong 200)"; if ($r.status -ne 200) { $ok = $false }
    } else { $ok = $false }
    $r1 = Api 'GET' '/books?keyword=Laravel' $null $null
    $r2 = Api 'GET' '/books?keyword=Laravel' $null $null
    $xc = if ($r2.headers) { [string]$r2.headers['X-Cache'] } else { '' }
    $notes += "GET /books lan 2 X-Cache=$xc"
    @{ ok = $ok; note = ($notes -join '; ') }
}

if ($server -and -not $server.HasExited) { Stop-Process -Id $server.Id -Force -ErrorAction SilentlyContinue }
$total.Stop()

Log ""
Log "================ TOM TAT ================"
$results | Format-Table Buoc, Ten, KetQua, Giay, GhiChu -AutoSize -Wrap | Out-String | ForEach-Object { Log $_ }
$min = [math]::Round($total.Elapsed.TotalMinutes, 1)
$fail = ($results | Where-Object { $_.KetQua -ne 'DAT' }).Count
Log ("Tong thoi gian: {0} phut (gioi han 30 phut)" -f $min)
Log ("So buoc loi: {0}/{1}" -f $fail, $results.Count)
Log $(if ($fail -eq 0 -and $min -le 30) { 'KET LUAN: DUNG LAI THANH CONG' } else { 'KET LUAN: CHUA DAT - xem cac buoc LOI o tren' })
Log "Log da luu: $logFile"
