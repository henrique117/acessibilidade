<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Checklist;
use App\Models\Diretriz;
use App\Models\Criterio;
use App\Models\Item;
use App\Models\Erro;
use App\Models\Image;
use Illuminate\Support\Facades\Auth;
use App\Models\Demandas;
use Illuminate\Support\Facades\Session;

class RelatorioController extends Controller
{
    public function index(Request $request){

        $demandaId = $request->cookie('demanda_authenticated');
        
        $demanda = Demandas::find($demandaId);

        if($demanda){
            
            $usuario = Auth::user();
            $erros =  Erro::where('avaliacao_id', $demandaId)->get();
            $search = $request->search;
            $opcaoEscolhida = $request->diretriz;
            $tipoEscolhido = $request->tipo;
            $itensBusca = null;
            $categorias = Checklist::all();

            $itens = Item::all();
            $tem_erro = array();
            $avaliacao = array();
            $paginas_Erro = array();

            $paginas = $demanda->paginas;

            if(isset($erros)){
                foreach($itens as $item){
                    foreach($erros as $erro){
                        if($erro->id_item === $item->id){
                            $tem_erro["$item->id"] = $erro;
                            $paginas_Erro["$item->id"] = [];
                            for($i = 0;$i<strlen($erro->pgs);$i++){
                                array_push($paginas_Erro["$item->id"],$paginas[$erro->pgs[$i]]);
                            }
                            $tem_erro["$item->id"]->images = Image::where('id_erro','=',$erro->id)->get();
                            $avaliacao["$item->id"] = $erro->em_cfmd;
                        }
                    }
                }
            }

            if($search !== null){
                $itensBusca = Item::where([
                    ['descricao', 'like', '%'.$search.'%']
                ])->get();
                }
            else{
                if($opcaoEscolhida === "wcag"){
                    $categorias = Diretriz::all();
                }
            }
            

            return view('welcome', ['itensBusca' => $itensBusca,'search' => $search, 'categorias' => $categorias, 'opcaoEscolhida' => $opcaoEscolhida,'tem_erro' => $tem_erro,'avaliacao' => $avaliacao,'tipo' => $tipoEscolhido,'usuario'=> $usuario,'id'=>$demanda->id,'demanda'=>$demanda,'pgs'=>$paginas_Erro]);
        }
        else{
            return redirect()->route('demanda.mostrar');
        }
    }
}
