// resources/views/admin/thong_ke/pdf.blade.php

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Báo cáo thống kê mượn sách</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h2 {
            margin: 0;
            color: #2c3e50;
        }
        .section {
            margin-bottom: 20px;
        }
        .section h3 {
            color: #34495e;
            border-bottom: 1px solid #bdc3c7;
            padding-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f6fa;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-style: italic;
        }
        .page-break {
            page-break-after: always;
        }
        .tong-quan {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .tong-quan-item {
            text-align: center;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
            width: 23%;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>BÁO CÁO THỐNG KÊ MƯỢN SÁCH</h2>
        <p>Tháng {{ $thang }} năm {{ $nam }}</p>
    </div>

    <div class="section">
        <h3>1. Thông tin tổng quan</h3>
        <div class="tong-quan">
            <div class="tong-quan-item">
                <h4>Tổng phiếu mượn</h4>
                <p>{{ $thongKeChiTiet['tong_phieu'] }}</p>
            </div>
            <div class="tong-quan-item">
                <h4>Tổng sách mượn</h4>
                <p>{{ $thongKeChiTiet['tong_sach_muon'] }}</p>
            </div>
            <div class="tong-quan-item">
                <h4>Phiếu quá hạn</h4>
                <p>{{ $thongKeChiTiet['phieu_qua_han'] }}</p>
            </div>
            <div class="tong-quan-item">
                <h4>Tổng tiền phạt</h4>
                <p>{{ number_format($thongKeChiTiet['tong_tien_phat']) }}đ</p>
            </div>
        </div>
    </div>

    <div class="section">
        <h3>2. Thống kê theo danh mục</h3>
        <table>
            <tr>
                <th>Danh mục</th>
                <th>Số lượt mượn</th>
                <th>Tỷ lệ</th>
            </tr>
            @foreach($thongKeChiTiet['theo_danh_muc'] as $danhMuc => $soLuot)
            <tr>
                <td>{{ $danhMuc }}</td>
                <td>{{ $soLuot }}</td>
                <td>{{ round(($soLuot / $thongKeChiTiet['tong_sach_muon']) * 100, 1) }}%</td>
            </tr>
            @endforeach
        </table>
    </div>

    <div class="page-break"></div>

    <div class="section">
        <h3>3. Top độc giả mượn nhiều nhất</h3>
        <table>
            <tr>
                <th>STT</th>
                <th>Họ tên</th>
                <th>Số lượt mượn</th>
                <th>Tổng sách</th>
            </tr>
            @foreach($thongKeChiTiet['doc_gia_muon_nhieu'] as $index => $docGia)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $docGia['ten'] }}</td>
                <td>{{ $docGia['so_luot_muon'] }}</td>
                <td>{{ $docGia['tong_sach'] }}</td>
            </tr>
            @endforeach
        </table>
    </div>

    <div class="section">
        <h3>4. Top sách được mượn nhiều nhất</h3>
        <table>
            <tr>
                <th>STT</th>
                <th>Tên sách</th>
                <th>Số lượt mượn</th>
                <th>Tổng số lượng</th>
            </tr>
            @foreach($thongKeChiTiet['sach_muon_nhieu'] as $index => $sach)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $sach['tieu_de'] }}</td>
                <td>{{ $sach['so_luot_muon'] }}</td>
                <td>{{ $sach['tong_so_luong'] }}</td>
            </tr>
            @endforeach
        </table>
    </div>

    <div class="footer">
        <p>Ngày xuất báo cáo: {{ date('d/m/Y') }}</p>
        <p>Người xuất báo cáo: {{ Auth::user()->name }}</p>
    </div>
</body>
</html>