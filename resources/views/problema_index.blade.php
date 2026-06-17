<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    @vite(['resources/css/sessao.css','resources/css/problema.css', 'resources/js/problema.js'])
    <title>Problemas</title>
</head>
<body>
    <header>
        <a href="{{route('sessao',['id'=>$sessao->id])}}" class="voltar"><span class="material-symbols-outlined" data-cy="sair">logout</span>SESSÃO DE TESTE</a>
        <div class="tituloseparar">
            <h2>PROBLEMAS</h2>
            <h3>SESSÃO: {{$sessao->titulo}}</h3>
        </div>
        <div class="botaoSalvarPagina">
        </div>
    </header>

    <div class="modal" id="modalVisualizarProblema">
        <div class="conteudo-modal conteudoModalVisualizar">
            <span class="fecharVisualizar"><img src="img/fechar.png" class = "fecharVisualizar"alt="fechar visualização"></span>
            <div class="tituloVisualizarProblema">VISUALIZAR PROBLEMA</div>
            <label class="tituloDescricaoDoProblema">DESCRICAO DO PROBLEMA</label>
            <div class="descricaoDoProblema" id="descricaoProblema"></div>
            <label class="tarefasRelacionadasModalVisualizar">TAREFAS RELACIONADAS</l>
            <div id="tarefasNoModalVisualizar" class="tarefasNoModalVisualizar">
                    
            </div>
        </div>

    </div>

    <div class="modal" id="modalCriarProblema">
        <div class="conteudo-modal">
            <span class="fecharVisualizar"><img src="img/fechar.png" class = "fecharVisualizar"alt="fechar visualização"></span>
            <div class="tituloCadastrarProblema">CADASTRAR PROBLEMA</div>
            <label for="">TITULO</label>
            <input type="text" id="tituloProblemaCadastrar" class="tituloProblema">
            <label for="">DESCRIÇÃO</label>
            <div id="editor"></div>
            <label for="">TAREFAS RELACIONADAS</label>
            <div class="tarefasR">
                @foreach ($tarefas as $tarefa)
                    <div class="tarefasEscolher">
                        <input type="checkbox" name="tarefasPresentes" data-id="{{$tarefa->id}}" class="tarefaCheckbox">
                        <div class="tarefaR">
                            <div class="tarefaTituloR">{{$tarefa->titulo}}</div>
                            <div class="tarefaDescricaoR">{!! $tarefa->descricao !!}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            <button id="botaoCadastrarProblema" class="botaoCadastrarProblema">CADASTRAR PROBLEMA</button>

        </div>
    </div>  

    <div class="modal" id="modalEditarProblema">
        <div class="conteudo-modal">
            <span class="fecharVisualizar"><img src="img/fechar.png" class = "fecharVisualizar"alt="fechar visualização"></span>
            <div class="tituloCadastrarProblema">EDITAR PROBLEMA</div>
            <label for="">TITULO</label>
            <input type="text" id="tituloProblemaEditar" class="tituloProblema">
            <label for="">DESCRIÇÃO</label>
            <div id="editorEditar"></div>
            <label for="">TAREFAS RELACIONADAS</label>
            <div class="tarefasR">
                @foreach ($tarefas as $tarefa)
                    <div class="tarefasEscolher">
                        <input type="checkbox" name="tarefasPresentes" data-id="{{$tarefa->id}}" class="tarefaCheckboxEditar">
                        <div class="tarefaR">
                            <div class="tarefaTituloR">{{$tarefa->titulo}}</div>
                            <div class="tarefaDescricaoR">{!! $tarefa->descricao !!}</div>
                        </div>
                    </div>
                @endforeach
                
            </div>
            <button id="botaoEditarProblema" class="botaoCadastrarProblema">EDITAR PROBLEMA</button>

        </div>
    </div>  

    <div class="modal" id="modalDeletarProblema">
        <div class="conteudo-modal modalDeletarProblema">
            <span class="fecharVisualizar"><img src="img/fechar.png" class = "fecharVisualizar excluirFechar"alt="fechar visualização"></span>
            <div class="TituloDeletarProblema">TEM CERTEZA QUE DESEJA DELETAR O PROBLEMA?</div>
            <div class="opcoesDeletarProblema">
                <button class="botaoOpcoesRemover removerProblemaBotaoTeste" id="removerProblemaBotao">SIM, TENHO CERTEZA</button>
                <button class="botaoOpcoesRemover voltarDesistir">NÃO, DESEJO VOLTAR</button>
            </div>
        </div>
    </div>

    <div class="tarefas">
        <img src="img/adicionar.png" alt="adicionar tarefa" class="adicionar" id="botaoAdicionarProblema">
        @foreach ($problemas as $problema)
            <div class="tarefa">
            <div class="visualizarTarefaJaCriada" id="abrirModal">
                        <img src="img/olho.png" alt="visulizar tarefa" data-id={{$problema->id}} class="olho">
                        <p>{!! $problema->titulo !!}</p>
            </div>
                        <div class="iconesTarefa">
                        <img src="img/lixeira.png" alt="excluir tarefa" data-id={{$problema->id}} class="lixeiraJaExiste">
                        <img src="img/lapis.png" alt="editar tarefa" data-id={{$problema->id}} class="lapisJaNoBanco" >
                </div>
            </div>
        @endforeach
    </div>  

    <script>
        variaveis = {
            problemas: @json($problemas),
            rotaEditar: "{{ route('problemasEditar') }}",
            rotaDeletar: "{{route('problemasRemover')}}",
            rotaAdicionar: "{{route('problemasAdicionar')}}",
            rotaUploadImagem: "{{route('problemaUploadImagem')}}",
            sessaoId: "{{$sessao->id}}"
        }

    </script>

    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
</body>
</html>