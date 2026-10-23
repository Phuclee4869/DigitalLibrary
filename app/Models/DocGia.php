<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocGia extends Model
{
    use HasFactory;

    protected $table = 'doc_gia';
    protected $fillable = [
        'ho_ten', 'ngay_sinh', 'dia_chi', 'email', 'so_dien_thoai',
        'ngay_dang_ky', 'ngay_het_han', 'trang_thai'
    ];

    protected $casts = [
        'ngay_sinh' => 'date',
        'ngay_dang_ky' => 'date',
        'ngay_het_han' => 'date',
    ];

    public function phieuMuons()
    {
        return $this->hasMany(PhieuMuon::class, 'doc_gia_id');
    }
}