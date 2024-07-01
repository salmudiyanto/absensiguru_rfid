<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    use HasFactory;

    public function users(){
        return $this->belongsTo(User::class, 'users_id');
    }

    public function kelas()
    {
        return $this->hasOne(Kelas::class, 'gurus_id')->onDelete('cascade');
    }
}
