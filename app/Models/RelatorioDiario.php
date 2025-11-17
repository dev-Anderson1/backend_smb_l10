<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelatorioDiario extends Model
{
    use HasFactory;

    protected $table = 'relatorios_diarios'; 

   protected $fillable = [
    'data',
    'instituicao',
    'oficial_dia_id',
    'respondente_id',
    'adjunto_id',
    'dia_smb_id',
    'dia_smb_entrada_id',
    'dia_smb_saida_id',
    'acontecimentos',
    'hora_entrada',
    'hora_saida',
];


    // 🔹 RELACIONAMENTOS
    public function oficialDia()
    {
        return $this->belongsTo(User::class, 'oficial_dia_id');
    }

    public function respondente()
    {
        return $this->belongsTo(User::class, 'respondente_id');
    }

    public function adjunto()
    {
        return $this->belongsTo(User::class, 'adjunto_id');
    }

    public function diaSmb()
    {
        return $this->belongsTo(User::class, 'dia_smb_id');
    }


    public function diaSmbEntrada()
{
    return $this->belongsTo(User::class, 'dia_smb_entrada_id');
}

public function diaSmbSaida()
{
    return $this->belongsTo(User::class, 'dia_smb_saida_id');
}




}
