<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADICIONAR ERRO DE ACESSIBILIDADE</title>
    @vite(entrypoints: ['resources/css/avaliacaoGuide.css','resources/js/avaliacaoGuide.js'])
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
</head>
<body>
    <header>
        <div class="iconeVoltarGrupo">
            <a href="/erro" class="iconeVoltarGrupo"><span class="material-symbols-outlined" data-cy="sair">logout</span>Ir para Avaliações</a>
        </div>
        <h1>ADICIONAR ERRO DE ACESSIBILIDADE</h1>
        <div class="iconeVoltarGrupo"></div>
    </header>

    <div class="oitem itemChecklist">
        <div class="oitemConteudo">
            <div class="informacoesDoItem">
                <h1>ITEM</h1>
                <p>{{$descricao}}</p>
                <h2>CRITÉRIO(S) WCAG</h2>
                @foreach ($criterios as $criterio)
                <p>{{$criterio->codigo}} ({{$criterio->conformidade}}): {{$criterio->nome}}</p>
                @endforeach
                <h2>Checklist ABNT:</h2>
                <p>{{$checklist}}</p>
            </div>
        </div>
    </div>
    <p id="teste" style="display: none;">{{$metodo}}</p>
    @if(isset($tem_erro->em_cfmd))<p id="teste1" style="display: none;">{{$tem_erro->em_cfmd}}</p>@endif
    <form method="POST" action=" {{route($rota,['id' =>  $id ,'id_demanda' =>$id_demanda])}} " id="myForm" enctype="multipart/form-data">
        @method($metodo)
        @csrf
        <div class="opcoesAvaliacaocheckComTitulo">
        <h1>O SITE SEGUE AS RECOMENDAÇÕES?</h1>
        <div class="opcoesAvaliacaocheck">
            <input id="labelforconforme" name="opcao" checked value=1 type="radio">
            <label for="conforme">SIM</label>
            <input id="labelfornaoconforme" name="opcao" value=2 type="radio">
            <label for="naoConforme">NÃO</label>
            <input id="labelfornaoseaplicaconforme" name="opcao" value=3 type="radio">
            <label for="naoAplicavel">NÃO SE APLICA</label>
        </div>
    </div>
        <div id="ocultarExibir">
            <div>
            <h1 class="tituloGerall">PÁGINAS</h1>
            <div class="paginasQuadrado">
                <div class="umaPagina">
                    @foreach ($paginas as $key=>$pg)
                    <div>
                        <input type="checkbox" value="{{$key}}" name="pgs[]" id="{{$key}}">
                        <label>{{ $pg['url'] }}</label>
                    </div>
                    @endforeach
                </div>

            </div>
        </div>
        
        <div>
            <h1 class="tituloGerall">DESCRIÇÃO DO PROBLEMA</h1>
            <div id="editor"></div>
            <input type="hidden" class="descricao" name="descricao" id="conteudoHidden">
        </div>

        <div>
            <h1 class="tituloGerall">NÍVEL DE CRITICIDADE</h1>
            <select name="criticidade" id="criticidade" style="width: 100%; padding: 10px; margin-bottom: 20px; font-size: 1rem; border: 0.3vw solid #3700DB; border-radius: 0.3vw;">
                <option value="Baixa" {{ (isset($tem_erro->criticidade) && $tem_erro->criticidade == 'Baixa') ? 'selected' : '' }}>Baixa</option>
                <option value="Média" {{ (isset($tem_erro->criticidade) && $tem_erro->criticidade == 'Média') ? 'selected' : (!isset($tem_erro->criticidade) ? 'selected' : '') }}>Média</option>
                <option value="Alta" {{ (isset($tem_erro->criticidade) && $tem_erro->criticidade == 'Alta') ? 'selected' : '' }}>Alta</option>
            </select>
        </div>

        <div>
            <h1 class="tituloGerall">COMPORTAMENTO ESPERADO</h1>
            <div id="editorComportamento" style="height: 8vw;"></div>
            <input type="hidden" name="comportamento_esperado" id="comportamentoHidden">
        </div>

        <div>
            <h1 class="tituloGerall">ADICIONAR IMAGEM</h1>
            <div class="adicionarImagem" id="imageUploadContainer">
                <button class="adicionar_imagem">ADICIONAR IMAGEM</button>
                @if(!empty($tem_erro->images[0]) and $tem_erro->em_cfmd == "2")
                <div class='anterior_quadrado'>
                    @foreach ($tem_erro->images as $imagens)
                        <img src="{{asset($imagens->path_image)}}" class="imagens">
                        <br>
                        <div>
                            <label for="remover_imagem" class="image_remove">Remover Imagem</label>
                            <input type="checkbox" name={{$imagens->id}} class="image_remove">
                        </div>
                        <br>
                    @endforeach
                </div>
            @endif
            </div>
        </div>
    </div>

    <button class="botaoFinal" id="botao_submition"type="submit">ENVIAR</button>
</form>
    <script>
        const tem_erro = @json($tem_erro);
        const paginas = @json($paginas);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toolbarOptions = [
                [{ 'header': [false, 1, 2, 3] }],
                ['bold', 'italic', 'underline'],
                ['link'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['clean']
            ];

            const quillComportamento = new Quill('#editorComportamento', {
                theme: 'snow',
                modules: {
                    toolbar: toolbarOptions
                }
            });

            const comportamentoExistente = @json($tem_erro->comportamento_esperado ?? '');
            if (comportamentoExistente) {
                quillComportamento.clipboard.dangerouslyPasteHTML(comportamentoExistente);
            }

            var form = document.getElementById('myForm');
            form.addEventListener('submit', function() {
                var comportamentoHidden = document.getElementById('comportamentoHidden');
                comportamentoHidden.value = quillComportamento.root.innerHTML;
            });
        });
    </script>
</body>
</html>