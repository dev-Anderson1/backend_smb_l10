<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\{
    OpmController, ArmaController, AuthController, UserController, AlgemaController, ColeteController,
    EspadaController, CalibreController, CautelaController, MunicaoController, CarregadorController,
    ModeloArmaController, PostoGraduacaoController, RelatorioDiarioController
};

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
