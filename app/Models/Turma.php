<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Turma extends Model
{
    use HasFactory;

   protected $fillable = ['tipo_curso', 'nome'];


    public function reservas()
    {
        return $this->hasMany(ReservaMunicao::class, 'turma_id');
    }
}
