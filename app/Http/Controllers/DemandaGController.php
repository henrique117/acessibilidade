<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Demandas;
use App\Models\Teste;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DemandaGController extends Controller
{
    public function index(Request $request){
        
        return view("demanda_cadastro");

    }

    public function store(Request $request){

        $paginasData = [];
        if ($request->has('guideliness') && $request->input('guideliness') == true) {
            $urls = $request->input('url', []);
            $nome_paginas = $request->input('nome_pagina', []);

            foreach ($urls as $key => $url) {
                if (!empty($url) && isset($nome_paginas[$key])) {
                    $paginasData[] = [
                        'url' => $url,
                        'pagina' => $nome_paginas[$key]
                    ];
                }
            }
        }
        
        try {
            DB::transaction(function () use ($request, $paginasData) {
                
                $demanda_criar = Demandas::create([
                    'nome' => $request->input('nome'),
                    'descricao' => $request->input('descricao'),
                    'password' => $request->input('senha'),
                    'status' => "Em andamento",
                    'paginas' => $paginasData,
                    'testeUsuario' => $request->has('testeComUsuario'),
                    'guideliness' => $request->has('guideliness'),
                ]);

                if ($demanda_criar->testeUsuario && $request->has('titulo')) {
                    Teste::create([
                        'titulo'=> $request->input('titulo'),
                        'dispositivo'=> $request->input('dispositivo'),
                        'avaliacao_id' => $demanda_criar->id,
                    ]);
                }
            });
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['db_error' => 'Erro ao salvar no banco: ' . $e->getMessage()]);
        }

        return redirect('/');
    }
}