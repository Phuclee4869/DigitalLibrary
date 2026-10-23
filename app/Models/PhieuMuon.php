<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhieuMuon extends Model
{
    use HasFactory;

    protected $table = 'phieu_muon';
    protected $fillable = [
        'doc_gia_id', 'user_id', 'ngay_muon', 'ngay_hen_tra',
        'ngay_tra', 'trang_thai', 'ghi_chu'
    ];

    protected $casts = [
        'ngay_muon' => 'date',
        'ngay_hen_tra' => 'date',
        'ngay_tra' => 'date',
    ];

    public function docGia()
    {
        return $this->belongsTo(DocGia::class, 'doc_gia_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function chiTietPhieuMuons()
    {
        return $this->hasMany(ChiTietPhieuMuon::class, 'phieu_muon_id');
    }
}