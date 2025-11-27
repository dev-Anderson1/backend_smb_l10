<?php

namespace App\Http\Controllers;

use App\Models\ReservaMunicao;
use App\Models\MunicaoSaldo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservaMunicaoController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($request->query('mine')) {
            $reservas = ReservaMunicao::with(['municao','turma','tipoAula'])->where('user_id', $user->id)->orderByDesc('created_at')->get();
        } else {
            // somente admins podem ver todas
            if (!$user->is_admin) return response()->json([], 403);
            $reservas = ReservaMunicao::with(['municao','user','turma','tipoAula'])->orderByDesc('created_at')->get();
        }

        return response()->json($reservas);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'turma' => 'nullable|string|max:255',
            'tipo_aula' => 'nullable|string|max:255',
            'turma_id' => 'nullable|exists:turmas,id',
            'tipo_aula_id' => 'nullable|exists:tipo_aulas,id',
            'municao_id' => 'required|exists:municoes,id',
            'alunos' => 'required|integer|min:1',
            'municoes_por_aluno' => 'required|integer|min:1',
        ]);

        $total = $data['alunos'] * $data['municoes_por_aluno'];

        return DB::transaction(function () use ($user, $data, $total) {
            // buscar saldo do instrutor para a munição selecionada
            $saldo = MunicaoSaldo::firstOrCreate(
                ['user_id' => $user->id, 'municao_id' => $data['municao_id']],
                ['quantidade' => 0]
            );

            if ($saldo->quantidade < $total) {
                return response()->json(['message' => 'Saldo insuficiente para esta munição'], 422);
            }

            // desconta imediatamente do saldo
            $saldo->quantidade -= $total;
            $saldo->save();

            $reserva = ReservaMunicao::create(array_merge($data, [
                'user_id' => $user->id,
                'total_municoes' => $total,
                'status' => 'pending',
            ]));

            return response()->json($reserva);
        });
    }

    public function approve($id, Request $request)
    {
        $user = $request->user();
        if (!$user->is_admin) return response()->json(['message' => 'Forbidden'], 403);

        $reserva = ReservaMunicao::findOrFail($id);
        if ($reserva->status !== 'pending') return response()->json(['message' => 'Reserva não pode ser aprovada'], 422);

        $reserva->status = 'approved';
        $reserva->approver_id = $user->id;
        $reserva->save();

        // Aqui poderíamos criar uma Cautela real e preencher cautela_numero

        return response()->json($reserva);
    }

    public function cancel($id, Request $request)
    {
        $user = $request->user();
        $reserva = ReservaMunicao::findOrFail($id);

        // somente o solicitante ou admin pode cancelar
        if ($reserva->user_id !== $user->id && !$user->is_admin) return response()->json(['message' => 'Forbidden'], 403);

        if (!in_array($reserva->status, ['pending'])) {
            return response()->json(['message' => 'Reserva não pode ser cancelada'], 422);
        }

        return DB::transaction(function () use ($reserva) {
            // restaurar saldo
            $saldo = MunicaoSaldo::firstOrCreate(
                ['user_id' => $reserva->user_id, 'municao_id' => $reserva->municao_id],
                ['quantidade' => 0]
            );
            $saldo->quantidade += $reserva->total_municoes;
            $saldo->save();

            $reserva->status = 'cancelled';
            $reserva->save();

            return response()->json(['message' => 'Cancelada']);
        });
    }

    public function devolucao($id, Request $request)
    {
        $user = $request->user();
        $data = $request->validate(['quantidade' => 'required|integer|min:0']);

        $reserva = ReservaMunicao::findOrFail($id);

        // somente instrutor que solicitou ou admin pode registrar devolução
        if ($reserva->user_id !== $user->id && !$user->is_admin) return response()->json(['message' => 'Forbidden'], 403);

        if ($reserva->status !== 'approved' && $reserva->status !== 'completed') {
            // aceitar devolução mesmo que ainda não 'approved'? permitimos quando approved
            // se estiver pending, não faz sentido devolver
            return response()->json(['message' => 'Reserva não apta a devolução'], 422);
        }

        return DB::transaction(function () use ($reserva, $data) {
            $quant = intval($data['quantidade']);
            if ($quant <= 0) return response()->json(['message' => 'Quantidade inválida'], 422);
            $saldo = MunicaoSaldo::firstOrCreate(
                ['user_id' => $reserva->user_id, 'municao_id' => $reserva->municao_id],
                ['quantidade' => 0]
            );
            $saldo->quantidade += $quant;
            $saldo->save();

            // opcional: marcar reserva como completed se devolveu tudo
            // não alteramos total_municoes, apenas permitimos devolução parcial

            return response()->json(['message' => 'Devolução registrada', 'quantidade' => $quant]);
        });
    }
}
