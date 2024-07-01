<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absen extends Model
{
    use HasFactory;
    public function kelas(){
        return $this->belongsTo(Kelas::class, 'kelas_is');
    }
}
