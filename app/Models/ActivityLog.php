<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\models\User;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'activity',
        'old_data',
        'new_data',
    ];

    // Relasi many-to-one: log aktivitas dimiliki oleh satu user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
