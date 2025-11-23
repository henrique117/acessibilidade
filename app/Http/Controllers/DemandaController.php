<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Demandas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;

class DemandaController extends Controller
{

    public function index(Request $request){
        $demandas = Demandas::all();
        $usuario = Auth::user();
        
        return view('demandas',['demandas' => $demandas, 'usuario' => $usuario, 'error' => $request->input('error')]);
    }

    public function armazenar(Request $request){

        $demanda = Demandas::find($request->input('id'));

        if (!$demanda) {
            // Caso a demanda não seja encontrada
            return redirect()->route('demanda.mostrar',['error' => 'Demanda não encontrada']);
        }
        
        if(Hash::check($request->input('password'), $demanda->password)){

            Cookie::queue('demanda_authenticated', $demanda->id, 240);

            // Retorne uma view de sucesso
            if($request->input("testes") == "true")
                return redirect()->route('teste',['demanda_Id'=>$request->id]);
            else{
                return redirect()->route('index.mostrar');
            }
        }
        else{
            // Senha incorreta
            return redirect()->route('demanda.mostrar',['error' => 'Senha incorreta']);
        }
        
    }

    public function show($id)
    {
        $demanda = Demandas::findOrFail($id);

        return view('components.demandas.demanda-relatorio', [
            'demanda' => $demanda
        ]);
    }
}