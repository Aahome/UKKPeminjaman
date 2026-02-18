<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\models\Borrowing;

class ReturnModel extends Model
{
    protected $table = 'returns';

    protected $fillable = [
        'borrowing_id',
        'return_date',
        'fine',
        'total_price',
        'created_by',
        'modified_by',
    ];

    // Relasi many-to-one: data pengembalian terkait dengan satu peminjaman
    public function borrowing()
    {
        return $this->belongsTo(Borrowing::class);
    }
}
