<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Models\Checklist;
use App\Models\Diretriz;
use App\Models\Criterio;
use App\Models\Erro;
use App\Models\Image;
use App\Models\Item;
use Illuminate\Database\Schema\ForeignKeyDefinition;
use Illuminate\Support\Facades\Storage;
use App\Models\Demandas;

use function Laravel\Prompts\search;

class AvaliacaoController extends Controller
{

    public function index(Request $request, $id = 0){

        $demandaId = $request->cookie('demanda_authenticated');

        $erros =  Erro::where('avaliacao_id', $demandaId)->get();
        $final = array();

        foreach($erros as $erro){
            if($erro->id_item === intval($id)){
                $final = $erro;
                $final->images = Image::where('id_erro','=',$erro->id)->get();
            }
        }
        

        $rota = $request->input('rota');
        $metodo = $request->input('metodo');
        
        $paginas = array();

        
        if(isset($request->input('pgs')[$id])){
            $paginas = $request->input('pgs');
            $paginas = $paginas[$id];
        }

        $resultado = Item::find($id);

        $criterios = $resultado->criterios;
        
        $check_id = $resultado->checklist_id;

        $check_tudo = Checklist::find($check_id);

        $check_nome = $check_tudo->nome;

        $id_demanda = $request->input('demanda');

        $pgs = Demandas::where('id','=',$id_demanda)->first();
        $pgs = $pgs->paginas;

        return view("avaliacao", ['checklist' => $check_nome,'descricao' => $resultado->descricao,'criterios' => $criterios,'id'=> $id,'rota' => $rota,'metodo'=>$metodo,'tem_erro'=> $final,'id_demanda' => $id_demanda,'paginas' => $pgs,'pgs'=> $paginas]);
    }

    public function store(Request $request){

        $opcao = $request->input('opcao');
        $descricao = $request->input('descricao');
        $id = $request->input('id');
        
        $erro = new Erro();
        $erro->id_item = $id;
        $erro->em_cfmd = $opcao;
        $demanda = $request->input('id_demanda');
        $erro->avaliacao_id = $demanda;

        // Valores padrão para campos obrigatórios para evitar erro SQL se não for "Não Conforme"
        $erro->criticidade = 'Baixa';
        $erro->comportamento_esperado = ''; 
        // Normalmente descrição também precisa de valor se for NOT NULL
        if(empty($erro->descricao)) $erro->descricao = '';

        if($erro->em_cfmd == '2'){
            $erro->descricao = $descricao;
            
            // Novos Campos
            $erro->criticidade = $request->input('criticidade');
            $erro->comportamento_esperado = $request->input('comportamento_esperado');

            $paginas = "";
            $erro->pgs = $request->input('pgs');
            if(is_array($erro->pgs)){
                foreach($erro->pgs as $pgs){
                    $paginas = $paginas.$pgs;
                }
            }
            $erro->pgs = $paginas;
            
            $erro->save(); 

            if($request->hasFile('imagens')) {
                $Imagens = $request->file('imagens');
                foreach($Imagens as $requestImage){
                    if($requestImage->isValid()){
                
                        $extension = pathinfo($requestImage->getClientOriginalName(), PATHINFO_EXTENSION);
                
                        $imageName = md5($requestImage->getClientOriginalName()) . strtotime('now') . '.' . $extension;
                
                        $requestImage->move(public_path('img/erros'), $imageName);
            
                        $imagePath = '/img/erros' . '/' . $imageName;
                
                        $imagem = new Image();
                        $imagem->id_erro = $erro->id; 
                        $imagem->path_image = $imagePath;
                        $imagem->save();
                    }
                }
            }
        }
        
        $erro->save(); 

        return redirect()->route('index.mostrar');
    }

    public function remove(Request $request){

       $id_item = $request->input('id');
       
       //Pegando o id do item para encontrar o erro correspondente

       $demandaId = $request->cookie('demanda_authenticated');
       
        $erro = Erro::where('id_item','=',$id_item)->where('avaliacao_id', $demandaId)->first();

        //Pegando o array de imagens que estão relacionadas ao erro

        $imagens_deletar = Image::where('id_erro','=',$erro->id)->get(); 

        // Excluindo cada uma

        foreach($imagens_deletar as $imagens){
            $caminho = str_replace('/',DIRECTORY_SEPARATOR,$imagens->path_image);
            if(file_exists(public_path('').$caminho)){
                unlink(public_path('').$caminho);
            }
        }

        //Excluindo as imagens no banco de dados

        Image::where('id_erro','=',$erro->id)->delete();

        //Excluindo o erro
        
        $erro->delete();

        return redirect()->route('index.mostrar');

    }

    public function update(Request $request){

        $demandaId = $request->cookie('demanda_authenticated');

        //Capturando o erro, utiliza-se o first por ser certeza de só existir um erro por item

        $erro = Erro::where('id_item','=',$request->input('id'))->where('avaliacao_id', $demandaId)->first();


        // Verificando se as opções estão nulas

        if(null !== $request->input('opcao')){ 
            $erro->em_cfmd = $request->input('opcao');
        }
        
        if($erro->em_cfmd == "2"){

            if(null !== $request->input('descricao')){
                $erro->descricao = $request->input('descricao');
            }
            
            // Novos Campos - Atualização
            if(null !== $request->input('criticidade')){
                $erro->criticidade = $request->input('criticidade');
            }
            if(null !== $request->input('comportamento_esperado')){
                $erro->comportamento_esperado = $request->input('comportamento_esperado');
            }

            if(null !== $request->input('pgs')){
                $paginas = "";
                $erro->pgs = $request->input('pgs');
                if(is_array($erro->pgs)){
                    foreach($erro->pgs as $pgs){
                        $paginas = $paginas.$pgs;
                    }
                }
                $erro->pgs = $paginas;
            }
        }
        else{
            $erro->pgs = '';
            $erro->descricao = '';
            // Resetar novos campos se não houver erro
            $erro->criticidade = 'Baixa';
            $erro->comportamento_esperado = '';
        }

        
        //Primeiro verifica se o usuário enviou alguma imagem

        if($erro->em_cfmd == "2"){
                //Pega e deleta as imagens

                $imagens_deletar = array();
                $imagens_ver = Image::where('id_erro','=',$erro->id)->get();

                foreach($imagens_ver as $imagens){
                    if((null !== $request->input($imagens->id)) and $request->input($imagens->id) == "on"){
                        array_push($imagens_deletar, $imagens);
                    }
                }

                foreach($imagens_deletar as $imagens){
                    Image::where('id','=',$imagens->id)->delete();
                    $caminho = str_replace('/',DIRECTORY_SEPARATOR,$imagens->path_image);
                    if(file_exists(public_path('').$caminho)){
                        unlink(public_path('').$caminho);
                    }
                }

                //Agora realiza o mesmo passo do armazenamento, que é colocar as imagens tanto em arquivo quanto no banco de dados
                if($request->hasFile('imagens') and null !== $request->file('imagens')) { 
                    $Imagens = $request->file('imagens'); //Pega as imagens do Front

                //Para cada imagem recebida, armazena o caminho no banco e move a imagem para um diretório
                
                foreach($Imagens as $requestImage){ 
                    if($requestImage->isValid()){
                
                        $extension = pathinfo($requestImage->getClientOriginalName(), PATHINFO_EXTENSION);
                
                        $imageName = md5($requestImage->getClientOriginalName()) . strtotime('now') . '.' . $extension;
                
                        $requestImage->move(public_path('img/erros'), $imageName);
                
                        $imagePath = '/img/erros' . '/' . $imageName;
                
                        $imagem = new Image();
                        $imagem->id_erro = $erro->id;
                        $imagem->path_image = $imagePath;
                        $imagem->save();}

            }}}
            else{
                $imagens_deletar = Image::where('id_erro','=',$erro->id)->get();

                foreach($imagens_deletar as $imagens){
                    $caminho = str_replace('/',DIRECTORY_SEPARATOR,$imagens->path_image);
                    if(file_exists(public_path('').$caminho)){
                        unlink(public_path('').$caminho);
                    }
                }

                Image::where('id_erro','=',$erro->id)->delete();
            }

        $erro->update();
        return redirect()->route('index.mostrar');
        

    }
        

}