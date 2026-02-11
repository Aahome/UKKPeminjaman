<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tool extends Model
{
    protected $fillable = [
        'tool_name',
        'category_id',
        'stock',
        'condition',
    ];

    // Relasi many-to-one: alat termasuk dalam satu kategori
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relasi one-to-many: satu alat dapat dipinjam berkali-kali
    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }
}
