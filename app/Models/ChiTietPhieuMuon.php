<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChiTietPhieuMuon extends Model
{
    use HasFactory;

    protected $table = 'chi_tiet_phieu_muon';
    protected $fillable = [
        'phieu_muon_id', 'sach_id', 'so_luong',
        'tinh_trang_khi_muon', 'tinh_trang_khi_tra',
        'tien_phat', 'ghi_chu'
    ];

    public function phieuMuon()
    {
        return $this->belongsTo(PhieuMuon::class, 'phieu_muon_id');
    }

    public function sach()
    {
        return $this->belongsTo(Sach::class, 'sach_id');
    }
}