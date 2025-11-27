<?php

namespace App\Models;

use App\Models\User;
use App\Models\Municao;
use App\Models\Turma;
use App\Models\TipoAula;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReservaMunicao extends Model
{
    use HasFactory;

    protected $table = 'reservas_municoes';
    protected $fillable = ['user_id','municao_id','turma','tipo_aula','turma_id','tipo_aula_id','alunos','municoes_por_aluno','total_municoes','status','approver_id','cautela_numero'];

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

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
