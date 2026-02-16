<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Role;
use App\Models\Grade;
use App\Models\Borrowing;
use App\Models\ActivityLog;


class User extends Authenticatable
{
    use Notifiable;
    protected $fillable = [
        'name',
        'username',
        'email',
        'phone_number',
        'password',
        'role_id',
        'grade_id',
        'created_by',
        'modified_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Relasi many-to-one: user dimiliki oleh satu role
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // Relasi many-to-one: user dimiliki oleh satu grade
    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    // Relasi one-to-many: user memiliki banyak data peminjaman
    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }

    // Relasi one-to-many: user memiliki banyak log aktivitas
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }
}
