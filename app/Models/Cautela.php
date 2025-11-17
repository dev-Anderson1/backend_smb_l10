<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cautela extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'user_id',
        'user_confirm_id',
        'status',
        'is_obsolete',
         'devolvido_por_id',
    ];

    protected $casts = [
    'created_at' => 'datetime:d/m/Y H:i',
    'updated_at' => 'datetime:d/m/Y H:i',
];



    // Usuário administrador que criou a cautela
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    // Usuário que recebeu a cautela
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Usuário que confirmou a cautela (finalização)
    // public function usuarioConfirmacao()
    // {
    //     return $this->belongsTo(User::class, 'user_confirm_id');
    // }

    // Itens da cautela
    public function itens()
    {
        return $this->hasMany(CautelaItem::class);
    }

    public function userConfirm()
    {
        return $this->belongsTo(User::class, 'user_confirm_id');
    }

    public function devolvidoPor()
    {
        return $this->belongsTo(User::class, 'devolvido_por_id');
    }



}
