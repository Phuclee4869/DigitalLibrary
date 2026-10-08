<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ thống Báo cáo & Thống kê Thư viện Số</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">
    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Hệ thống Báo cáo & Thống kê Thư viện (Buổi 7)</h2>
            <a href="{{ route('reports.export_excel') }}" class="btn btn-success shadow-sm">📥 Xuất Tệp Báo Cáo (Excel/CSV)</a>
        </div>

        <!-- BÁO CÁO 1: Thống kê Tổng quan (Dashboard Summary) -->
        <div class="row text-white mb-4">
            <div class="col-md-3">
                <div class="card bg-primary p-3 shadow-sm">
                    <h5>Tổng đầu sách</h5>
                    <h3>{{ $summary->tong_so_sach }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success p-3 shadow-sm">
                    <h5>Sách còn trong kho</h5>
                    <h3>{{ $summary->tong_sach_con_lai }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info p-3 shadow-sm">
                    <h5>Độc giả hoạt động</h5>
                    <h3>{{ $summary->tong_doc_gia }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-dark p-3 shadow-sm">
                    <h5>Phiếu đang mượn</h5>
                    <h3>{{ $summary->tong_phieu_dang_muon }}</h3>
                </div>
            </div>
        </div>

        <!-- KHU VỰC BIỂU ĐỒ TRỰC QUAN CHO CÁC BÁO CÁO -->
        <div class="row">
            <!-- BÁO CÁO 2: Biểu đồ Cột - Top Sách Mượn Nhiều Nhất -->
            <div class="col-md-6 mb-4">
                <div class="card p-3 shadow-sm h-100">
                    <h5 class="text-center text-primary mb-3">Báo cáo 2: Top 10 Sách Mượn Nhiều Nhất</h5>
                    <canvas id="topBooksChart"></canvas>
                </div>
            </div>

            <!-- BÁO CÁO 3: Biểu đồ Đường & Cột - Xu hướng Mượn Sách & Tiền Phạt Theo Tháng -->
            <div class="col-md-6 mb-4">
                <div class="card p-3 shadow-sm h-100">
                    <h5 class="text-center text-danger mb-3">Báo cáo 3: Xu Hướng Mượn & Tiền Phạt Theo Tháng</h5>
                    <canvas id="monthlyTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Cấu hình hiển thị Chart.js -->
    <script>
        const topBooksData = @json($topBooks);
        const monthlyData = @json($monthlyStats);

        // 1. Biểu đồ cho Báo cáo 2 (Top sách)
        new Chart(document.getElementById('topBooksChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: topBooksData.map(item => item.ten_sach),
                datasets: [{
                    label: 'Lượt mượn',
                    data: topBooksData.map(item => item.tong_luot_muon),
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });

        // 2. Biểu đồ cho Báo cáo 3 (Xu hướng mượn sách theo tháng)
        new Chart(document.getElementById('monthlyTrendChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: monthlyData.map(item => item.thang),
                datasets: [
                    {
                        label: 'Tổng phiếu mượn',
                        data: monthlyData.map(item => item.tong_phieu_muon),
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        yAxisID: 'y',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Tiền phạt (VNĐ)',
                        data: monthlyData.map(item => item.tong_tien_phat),
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        yAxisID: 'y1',
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: { type: 'linear', display: true, position: 'left', beginAtZero: true },
                    y1: { type: 'linear', display: true, position: 'right', grid: { drawOnChartArea: false }, beginAtZero: true }
                }
            }
        });
    </script>
</body>
</html>