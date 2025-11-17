<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Arma extends Model
{
    use HasFactory;

    protected $fillable = [
        'modelo_id',
        'numero_serie',
        'quantidade_carregadores',
        'situacao',
    ];

    public function modelo()
    {
        return $this->belongsTo(ModeloArma::class, 'modelo_id');
    }
}

