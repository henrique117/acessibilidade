<?php
require __DIR__ . '/crud/avaliacaoTarefas.php';
require __DIR__ . '/crud/demanda.php';
require __DIR__ . '/crud/erro.php';
require __DIR__ . '/crud/problema.php';
require __DIR__ . '/crud/sessao.php';
require __DIR__ . '/crud/tarefa.php';
require __DIR__ . '/crud/testeUsuario.php';
require __DIR__ . '/crud/relatorio.php';


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DemandaController;

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/demanda/gerarRelatorio/{id}', [DemandaController::class, 'show'])->name('demandas.gerarRelatorio');
    Route::post('/demanda/entrar', [DemandaController::class, 'entrarNaDemanda'])->name('demandas.entrar');

});
