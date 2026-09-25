<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = ['user_id', 'action', 'ip_address'];

    public static function record(?int $userId, string $action): void
    {
        self::create([
            'user_id'    => $userId,
            'action'     => $action,
            'ip_address' => request()->ip(),
        ]);
    }
}