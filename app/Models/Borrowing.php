<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\models\User;
use App\models\Tool;
use App\models\ReturnModel;

class Borrowing extends Model
{
    protected $fillable = [
        'user_id',
        'tool_id',
        'quantity',
        'borrow_date',
        'due_date',
        'status',
        'rejection_reason',
        'created_by',
        'modified_by',
    ];

    // Relasi many-to-one: peminjaman dilakukan oleh satu user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi many-to-one: peminjaman untuk satu alat
    public function tool()
    {
        return $this->belongsTo(Tool::class);
    }

    // Relasi one-to-one: satu peminjaman memiliki satu data pengembalian
    public function returnData()
    {
        return $this->hasOne(ReturnModel::class);
    }
}
