<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DanhMuc extends Model
{
    use HasFactory;

    protected $table = 'danh_muc';
    protected $fillable = ['ten_danh_muc', 'mo_ta'];

    public function sachs()
    {
        return $this->hasMany(Sach::class, 'danh_muc_id');
    }
}