<?php

namespace App\Models;

use App\Models\Arma;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Carregador extends Model
{
    use HasFactory;

    protected $table = 'carregadores';
    protected $fillable = ['modelo_id', 'capacidade', 'quantidade'];

    public function arma()
    {
        return $this->belongsTo(Arma::class);
    }

    public function modelo()
    {
        return $this->belongsTo(ModeloArma::class, 'modelo_id');
    }
}