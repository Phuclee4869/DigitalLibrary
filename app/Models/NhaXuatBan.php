<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NhaXuatBan extends Model
{
    use HasFactory;

    protected $table = 'nha_xuat_ban';
    protected $fillable = ['ten_nxb', 'dia_chi', 'email', 'so_dien_thoai'];

    public function sachs()
    {
        return $this->hasMany(Sach::class, 'nha_xuat_ban_id');
    }
}