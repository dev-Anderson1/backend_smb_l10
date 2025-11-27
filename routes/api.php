<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\{
    OpmController, ArmaController, AuthController, UserController, AlgemaController, ColeteController,
    EspadaController, CalibreController, CautelaController, MunicaoController, CarregadorController,
    ModeloArmaController, PostoGraduacaoController, RelatorioDiarioController
};
use App\Http\Controllers\ReservaMunicaoController;
use App\Http\Controllers\TurmaController;
use App\Http\Controllers\TipoAulaController;
use App\Http\Controllers\InstrutorController;
use App\Http\Controllers\InstrutorTurmaController;
use App\Http\Controllers\InstrutorSaldoController;

/*
|--------------------------------------------------------------------------
| ROTAS PÚBLICAS
|--------------------------------------------------------------------------
*/

Route::post('/login', 'App\Http\Controllers\AuthController@login');
Route::post('/register', [UserController::class, 'register']);

Route::get('/health', fn () => [
    'ok' => true,
    'app' => config('app.name'),
    'env' => config('app.env'),
    'time' => now()->toISOString(),
]);
Route::post('/logout', [AuthController::class, 'logout']);
/*
|--------------------------------------------------------------------------
| ROTAS AUTENTICADAS
|--------------------------------------------------------------------------
*/

Route::middleware('auth:api')->group(function () {

    // Auth
    
    Route::get('/me', [AuthController::class, 'me']);

    // Users CRUD
    Route::apiResource('users', UserController::class);
    Route::delete('/users/{id}/obsolete', [UserController::class, 'markAsObsolete']);

    // Armas
    Route::get('/armas/disponiveis', [ArmaController::class, 'armasDisponiveis']);
    Route::apiResource('armas', ArmaController::class);

    // Carregadores
    Route::post('/carregadores/emprestar/{armaId}', [CarregadorController::class, 'emprestarCarregador']);
    Route::post('/carregadores/devolver/{armaId}', [CarregadorController::class, 'devolverCarregador']);
    Route::apiResource('carregadores', CarregadorController::class);

    // Cautelas
    Route::get('/cautelas', [CautelaController::class, 'index']);
    Route::get('/cautelas/{id}', [CautelaController::class, 'show']);
    Route::post('/cautela/store', [CautelaController::class, 'store']);
    Route::post('/cautela/devolucao', [CautelaController::class, 'devolucao']);
    Route::post('/cautelas/{id}/devolver-item', [CautelaController::class, 'devolverItem']);
    Route::post('/cautelas/{id}/devolver-todos', [CautelaController::class, 'devolverTodos']);
    Route::get('/usuarios-com-cautelas-pendentes', [CautelaController::class, 'usuariosComCautelasPendentes']);
    Route::get('/usuarios/{id}/cautelas-pendentes', [CautelaController::class, 'getCautelasPorUsuario']);
    Route::get('/me/cautelas-pendentes', [CautelaController::class, 'cautelasPendentesDoUsuarioAutenticado']);

    // Outros resources
    Route::apiResource('espadas', EspadaController::class);
    Route::apiResource('modelo_armas', ModeloArmaController::class);
    Route::apiResource('municoes', MunicaoController::class);
    Route::apiResource('posto_graduacoes', PostoGraduacaoController::class);
    Route::apiResource('algemas', AlgemaController::class);
    Route::apiResource('opms', OpmController::class);
    Route::apiResource('calibres', CalibreController::class);
    Route::apiResource('coletes', ColeteController::class);


    Route::get('/instrutores', [InstrutorController::class, 'index']);
    Route::post('/instrutores', [InstrutorController::class, 'store']);
    Route::put('/instrutores/{id}', [InstrutorController::class, 'update']);
    Route::delete('/instrutores/{id}', [InstrutorController::class, 'destroy']);

    // TURMAS DO INSTRUTOR
    Route::get('/instrutores/{id}/turmas', [InstrutorTurmaController::class, 'index']);
    Route::post('/instrutores/{id}/turmas', [InstrutorTurmaController::class, 'store']);
    Route::delete('/instrutores/{id}/turmas/{turmaId}', [InstrutorTurmaController::class, 'destroy']);

    // SALDO DO INSTRUTOR
    // Lista todos os instrutores com seus saldos (admin)
    Route::get('/instrutores/saldos', [InstrutorSaldoController::class, 'all']);
    Route::get('/instrutores/{id}/saldos', [InstrutorSaldoController::class, 'index']);
    Route::post('/instrutores/{id}/saldos', [InstrutorSaldoController::class, 'adicionarSaldo']);
    Route::put('/instrutores/saldos/{saldo}', [InstrutorSaldoController::class, 'updateSaldo']);
    Route::post('/instrutores/saldos', [InstrutorSaldoController::class, 'adicionarSaldoGlobal']);



    // SALDO DO PRÓPRIO INSTRUTOR
    Route::get('/me/saldos', [InstrutorSaldoController::class, 'meusSaldos']);

    // Reservas de munições
    Route::get('/reservas_municoes', [ReservaMunicaoController::class, 'index']);
    Route::post('/reservas_municoes', [ReservaMunicaoController::class, 'store']);
    Route::post('/reservas_municoes/{id}/approve', [ReservaMunicaoController::class, 'approve']);
    Route::post('/reservas_municoes/{id}/cancel', [ReservaMunicaoController::class, 'cancel']);
    Route::post('/reservas_municoes/{id}/devolucao', [ReservaMunicaoController::class, 'devolucao']);

    // Turmas e tipos de aula (listagem para selects no frontend)
   Route::get('/turmas', [TurmaController::class, 'index']);
    Route::post('/turmas', [TurmaController::class, 'store']);
    Route::put('/turmas/{id}', [TurmaController::class, 'update']);
    Route::delete('/turmas/{id}', [TurmaController::class, 'destroy']);


    Route::get('/tipo_aulas', [TipoAulaController::class, 'index']);
    Route::post('/tipo_aulas', [TipoAulaController::class, 'store']);

    // Relatórios
    Route::post('/relatorios_diarios', [RelatorioDiarioController::class, 'store']);
    Route::get('/relatorios_diarios/{id}', [RelatorioDiarioController::class, 'show']);
    Route::get('/relatorios_diarios/{id}/pdf', [RelatorioDiarioController::class, 'gerarPdf']);

});

/*
|--------------------------------------------------------------------------
| Teste de autenticação
|--------------------------------------------------------------------------
*/

Route::middleware('auth:api')->get('/check', fn (Request $request) => $request->user());
