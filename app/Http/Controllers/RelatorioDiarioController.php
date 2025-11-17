<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RelatorioDiario;
use App\Models\User;
 use Barryvdh\DomPDF\Facade\Pdf;

class RelatorioDiarioController extends Controller
{
    public function store(Request $request)
    {
       $request->validate([
    'data' => 'required|date',
    'instituicao' => 'nullable|string|max:255',
    'oficial_dia_id' => 'nullable|exists:users,id',
    'respondente_id' => 'nullable|exists:users,id',
    'adjunto_id' => 'nullable|exists:users,id',
    'dia_smb_id' => 'nullable|exists:users,id',
    'dia_smb_entrada_id' => 'nullable|exists:users,id', // <- novo
    'dia_smb_saida_id'   => 'nullable|exists:users,id', // <- novo
    'acontecimentos' => 'nullable|string',
]);

$relatorio = RelatorioDiario::create([
    'data' => $request->data,
    'instituicao' => $request->instituicao,
    'oficial_dia_id' => $request->oficial_dia_id,
    'respondente_id' => $request->respondente_id,
    'adjunto_id' => $request->adjunto_id,
    'dia_smb_id' => $request->dia_smb_id,
    'dia_smb_entrada_id' => $request->dia_smb_entrada_id, // <- novo
    'dia_smb_saida_id'   => $request->dia_smb_saida_id,   // <- novo
    'acontecimentos' => $request->acontecimentos,
]);

return response()->json([
    'success' => true,
    'message' => 'Relatório diário salvo com sucesso!',
    'relatorio' => $relatorio->load([
        'oficialDia:id,apelido,name',
        'respondente:id,apelido,name',
        'adjunto:id,apelido,name',
        'diaSmb:id,apelido,name',
        'diaSmbEntrada:id,apelido,name', // <- novo
        'diaSmbSaida:id,apelido,name',   // <- novo
    ]),
]);

    }

    public function show($id)
    {
        $relatorio = RelatorioDiario::with([
    'oficialDia:id,apelido,name',
    'respondente:id,apelido,name',
    'adjunto:id,apelido,name',
    'diaSmb:id,apelido,name',
    'diaSmbEntrada:id,apelido,name', // <- novo
    'diaSmbSaida:id,apelido,name',   // <- novo
])->findOrFail($id);

return response()->json($relatorio);

    }


   

public function gerarPdf($id)
{
   $relatorio = RelatorioDiario::with([
    'oficialDia',
    'respondente',
    'adjunto',
    'diaSmb',
    'diaSmbEntrada',
    'diaSmbSaida',
])->findOrFail($id);


    $pdf = Pdf::loadView('relatorio-diario', compact('relatorio'))
              ->setPaper('a4', 'portrait');

    return $pdf->stream('Relatorio_Diario_' . $relatorio->data . '.pdf');
}

}
