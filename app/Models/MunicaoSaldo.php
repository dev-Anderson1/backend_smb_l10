<?php

namespace App\Models;

use App\Models\User;
use App\Models\Municao;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MunicaoSaldo extends Model
{
    use HasFactory;

    protected $table = 'municao_saldos';
    protected $fillable = ['user_id', 'municao_id', 'quantidade', 'turma_id', 'tipo_aula_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function municao()
    {
        return $this->belongsTo(Municao::class, 'municao_id');
    }

    public function turma()
    {
        return $this->belongsTo(Turma::class, 'turma_id');
    }

    public function tipoAula()
    {
        return $this->belongsTo(TipoAula::class, 'tipo_aula_id');
    }
}
