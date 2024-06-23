<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['room_number', 'level', 'is_available'];

    public function hospitalizations()
    {
        return $this->hasMany(Hospitalization::class);
    }
}
