<?php

namespace App\Http\Controllers;
use App\Models\Sessao;
use App\Models\Tarefa;
use App\Models\Problema;
use App\Models\TarefaProblema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProblemaController extends Controller
{

    public function index(Request $request){
        $sessao = Sessao::where('id',$request->sessao_id)->first();
        $tarefas = Tarefa::where('avaliacao_id',$sessao->avaliacao_id)->get();
        $problema = $problema = Problema::where('sessao_id', $sessao->id)
        ->with(['tarefas'])
        ->get();

        return view('problema_index',['tarefas'=>$tarefas,'sessao'=>$sessao,'problemas'=>$problema]);
    }

    public function store(Request $request){
        $arrayTarefas = explode(',',$request->tarefas);
        $problema = new Problema();
        $problema->sessao_id = $request->idSessao;
        $problema->descricao = $request->descricao;
        $problema->titulo = $request->titulo;

        $problema->save();

        if($request->tarefas == null){
            return redirect()->route('problemasVer',['sessao_id'=>$request->idSessao]);
        }
        foreach($arrayTarefas as $tarefa){
            $tarefaProblema = new TarefaProblema();
            $tarefaProblema->tarefa_id = $tarefa;
            $tarefaProblema->problema_id = $problema->id;
            $tarefaProblema->save();
        }
        return redirect()->route('problemasVer',['sessao_id'=>$request->idSessao]);
    }

    public function delete(Request $request){
        $problema = Problema::where('id',$request->id)->first();
        $problema->delete();
        return redirect()->route('problemasVer',['sessao_id'=>$request->idSessao]);
    }

    public function edit(Request $request){
        $problema = Problema::where('id',$request->id)->first();
        $problema->descricao = $request->descricao;
        $problema->save();
        TarefaProblema::where('problema_id',$request->id)->delete();
        if($request->tarefas == null){
            return redirect()->route('problemasVer',['sessao_id'=>$request->idSessao]);
        }
        $arrayTarefas = explode(',',$request->tarefas);
        foreach($arrayTarefas as $tarefa){
            $tarefaProblema = new TarefaProblema();
            $tarefaProblema->tarefa_id = $tarefa;
            $tarefaProblema->problema_id = $problema->id;
            $tarefaProblema->save();
        }

        return redirect()->route('problemasVer',['sessao_id'=>$request->idSessao]);
    }

    public function uploadImagem(Request $request){
        $request->validate([
            'image' => 'required|image|max:10240',
        ]);

        $path = $request->file('image')->store('problemas', 'public');

        return response()->json([
            'url' => Storage::url($path),
        ]);
    }
}
