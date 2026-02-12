<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\models\User;

class Role extends Model
{
    // Menentukan kolom yang bisa diisi
    protected $fillable = ['role_name'];
    
// Relasi one-to-many: satu data memiliki banyak User
    public function users() {
        return $this->hasMany(User::class);
    }
}
