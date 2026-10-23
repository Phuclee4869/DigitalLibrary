<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sach extends Model
{
    use HasFactory;

    protected $table = 'sach';
    protected $fillable = [
        'tieu_de', 'danh_muc_id', 'tac_gia_id', 'nha_xuat_ban_id',
        'mo_ta', 'so_trang', 'nam_xuat_ban', 'hinh_anh',
        'so_luong', 'so_luong_con_lai', 'gia'
    ];

    public function danhMuc()
    {
        return $this->belongsTo(DanhMuc::class, 'danh_muc_id');
    }

    public function tacGia()
    {
        return $this->belongsTo(TacGia::class, 'tac_gia_id');
    }

    public function nhaXuatBan()
    {
        return $this->belongsTo(NhaXuatBan::class, 'nha_xuat_ban_id');
    }
}