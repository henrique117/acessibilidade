<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProblemaController;


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/problemas',[ProblemaController::class,"index"])->name("problemasVer");

    Route::post('/problemas',[ProblemaController::class,"store"])->name("problemasAdicionar");

    Route::post('/problemas/upload-imagem',[ProblemaController::class,"uploadImagem"])->name("problemaUploadImagem");

    Route::delete('/problemas',[ProblemaController::class,"delete"])->name("problemasRemover");

    Route::put('/problemas',[ProblemaController::class,"edit"])->name("problemasEditar");

});