<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'activity',
    ];

    // Relasi many-to-one: log aktivitas dimiliki oleh satu user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
