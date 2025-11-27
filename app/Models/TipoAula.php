<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TipoAula extends Model
{
    use HasFactory;

    protected $table = 'tipo_aulas';
    protected $fillable = ['nome'];

    public function reservas()
    {
        return $this->hasMany(ReservaMunicao::class, 'tipo_aula_id');
    }
}
