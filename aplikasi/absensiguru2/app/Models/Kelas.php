<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    public function gurus(){
        return $this->belongsTo(Guru::class, 'gurus_id');
    }
}
