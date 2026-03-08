<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class CautelaItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cautela_id',
        'arma_id',
        'colete_id',
        'espada_id',
        'algema_id',
        'outros_materiais',
        'quantidade',
        'devolvido',
        'devolvido_por_id',
        'devolvido_em',
    ];

  protected $casts = [
    'created_at' => 'datetime:d/m/Y H:i',
    'updated_at' => 'datetime:d/m/Y H:i',
];



    // Cada item pertence a uma cautela
    public function cautela()
    {
        return $this->belongsTo(Cautela::class);
    }

    // Item arma (se existir)
    public function arma()
    {
        return $this->belongsTo(Arma::class);
    }

    // Item colete (se existir)
    public function colete()
    {
        return $this->belongsTo(Colete::class);
    }

    // Item espada (se existir)
    public function espada()
    {
        return $this->belongsTo(Espada::class);
    }

    // Item algema (se existir)
    public function algema()
    {
        return $this->belongsTo(Algema::class);
    }

    public function devolvidoPor()
    {
        return $this->belongsTo(User::class, 'devolvido_por_id');
    }
}
