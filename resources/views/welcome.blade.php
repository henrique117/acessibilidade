<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <title>Adicionar erro de acessibilidade guiado</title>
    @vite(entrypoints: ['resources/css/guide.css' ,'resources/js/guideliness.js'])
</head>
<body>

    <div class="modal" id="modalDeletarProblema">
        <div class="conteudo-modal modalDeletarProblema">
            <span class="fecharVisualizar"><img src="img/fechar.png" class = "fecharVisualizar excluirFechar"alt="fechar visualização"></span>
            <div class="TituloDeletarProblema">TEM CERTEZA QUE DESEJA DELETAR A AVALIAÇÃO DO ITEM?</div>
            <div class="opcoesDeletarProblema">
                <form id="deleteForm" action="{{ route('erro.remove') }}" method="POST">
                    <input type="hidden" id="id_valor" name="id">
                    @csrf
                    @method('DELETE')
                    <button class="botaoOpcoesRemover removerProblemaBotaoTeste" id="removerProblemaBotao">SIM, TENHO CERTEZA</button>
                </form>
                <button class="botaoOpcoesRemover voltarDesistir">NÃO, DESEJO VOLTAR</button>
            </div>
        </div>
    </div>
    
    <header>
        <div class="iconeVoltarGrupo">
            <a href="/" class="iconeVoltarGrupo"><span class="material-symbols-outlined" data-cy="sair">logout</span>Ir para Avaliações</a>
        </div>
        <h1>ADICIONAR ERRO DE ACESSIBILIDADE</h1>
        <div class="iconeVoltarGrupo"></div>
    </header>


<div id="avalicaoMenuSuspenso" class="menuSuspenso">

        <div class="barra">
            <p>INFORMAÇÕES</p>
            <img src="img/iconeSuspenso.png" alt="">
        </div>
        <div class="conteudoDiretriz">
        <div class="campos">
            <div class="campo"> 
                <label for="">AVALIAÇÃO</label>
                <p>{{$demanda->nome}}</p>
            </div>

            <div class="campo"> 
                <label for="">DESCRIÇÃO</label>
                <p>{{$demanda->descricao}}</p>
            </div>
        </div>
    </div>

    </div>






    <!--TRECHO DO GLOSSARIO-->
    <div id="glossarioMenuSuspenso" class="menuSuspenso">
        <div class="barra">
            <p>GLOSSÁRIO</p>
            <img src="img/iconeSuspenso.png" alt="">
        </div>
        <div class="conteudoDiretriz">
        <div class="infoGlossario">
            <p><strong>Elementos focáveis: </strong> elementos passíveis de receber foco do teclado. Elementos que só recebem foco por programação não são considerados elementos focáveis.elementos passíveis de receber foco do teclado. Elementos que só recebem foco por programação não são considerados elementos focáveis.</p>
            <p><strong>Foco do teclado: </strong> foco direcionado a um elemento diretamente por meio da interface do teclado.</p>
            <p><strong>Foco por programação: </strong>  foco direcionado a um elemento por meio de programação, sem o uso direto da interface do teclado. Compreende também foco direcionado por botões ou âncoras, ainda que acionados pelo teclado, assim como foco direcionado por tecnologias assistivas, ainda que utilizem a interface do teclado para operar.</p>
            <p><strong>Indicador de foco visível: </strong>sinal gráfico que indica visualmente o elemento em foco, comumente representado como uma moldura ao redor do elemento.</p>
            <p><strong>Ordem sequencial consistente: </strong>a ordem de navegação se mantém da mesma forma do início ao fim, sem mudanças bruscas. Exemplo: se a navegação é da esquerda para a direita e de cima para baixo, ela não pode mudar bruscamente salvo quando o usuário puder perceber.</p>
            <p><strong>Tecla modificadora: </strong>Tecla do teclado que é usada em conjunto com outras teclas para executar funções específicas ou atalhos. As teclas modificadoras mais comuns são: Shift, Ctrl, Alt, Alt gr, Win, Option e Fn.</p>
        </div>
        </div>
    </div>



    <div class="menuSuspenso">
        <div class="barra">
            <p>BUSCAR ITEM DO CHECKLIST</p>
            <img src="img/iconeSuspenso.png" alt="">
        </div>
        <div class="conteudoDiretriz">
        <input id="inputPesquisa" class="pesquisar" placeholder="Procure por um item..." type="text" name="" id="">
        <button id="botaoBusca" class="efetuarBusca">EFETUAR BUSCA</button>
    </div>
    </div>

   


            <div id="filtros" class="menuSuspenso">
                <div class="barra">
                    <p>FILTROS</p>
                    <img src="img/iconeSuspenso.png" alt="">
                </div>
            
                <div class="conteudoDiretriz">
                <div class="opcoesFiltro">
                    <form action="/erro" id="formFiltros" method="GET">
                        @csrf
                        <div>
                            <label for="opcaoAvaliacoes">TIPO DE AVALIAÇÃO: </label>
                            <!-- <select id="opcaoAvaliacoes" name="diretriz" onchange="this.form.submit()">
                                <option value="abnt" @if($opcaoEscolhida == 'abnt') selected @endif>ABNT</option>
                                <option value="wcag" @if($opcaoEscolhida == 'wcag') selected @endif>WCAG</option>
                            </select> -->
                        </div>
            
                        <div>
                            <label for="opcaoEstadoAvaliacoes">ESTADO DOS ITENS:</label>
                            <select id="opcaoEstadoAvaliacoes" name="tipo" onchange="this.form.submit()">
                                <option value="4" @if($tipo == '4') selected @endif>Todos</option>
                                <option value="5" @if($tipo == '5') selected @endif>Não Avaliado</option>
                                <option value="3" @if($tipo == '3') selected @endif>Não se Aplica</option>
                                <option value="2" @if($tipo == '2') selected @endif>Não está de Acordo</option>
                                <option value="1" @if($tipo == '1') selected @endif>Está de Acordo</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
        </DIV>

            <h1 class="tituloITENS">ITENS</h1>
                
        @foreach($categorias as $categoria)

        <div class="menuSuspenso CategoriaItens">
            <div class="barra Itens">
                <div class="barraPrimeirasInformacoes">
                    <p>{{ $categoria->codigo }} {{ $categoria->nome }}</p>
                    <img src="img/iconeSuspenso.png" alt="">
                </div>
                <div class="tituloItens">{{ $categoria->descricao }}</div>
            </div>
                <!--EXIBIÇÃO DOS ITENS ORDENANDO POR DIRETRIZES DO WCAG-->
                @if($opcaoEscolhida === "wcag")

                    <div class="conteudoDiretriz" id="conteudo{{ $categoria->id }}">
                        @foreach($categoria->criterios as $criterios)
                            @foreach($criterios->itens as $itemChecklist)
                                @if($tipo === '4' or (!isset($avaliacao["$itemChecklist->id"]) and $tipo == 5) or (isset($avaliacao["$itemChecklist->id"]) and $avaliacao["$itemChecklist->id"] == $tipo))
                                <div class="oitem itemChecklist">
                                    <div class="oitemConteudo">
                                        <div class="informacoesDoItem">
                                            <h1>ITEM</h1>
                                            <p class="descricaoItem">{{$itemChecklist->descricao}}</p>
                                            <h2>CRITÉRIO(S) WCAG</h2>
                                            @foreach ($itemChecklist->criterios as $criterio)
                                            <p>{{$criterio->codigo}} ({{$criterio->conformidade}}): {{$criterio->nome}}</p>
                                            @endforeach
                                            <h2>Checklist ABNT:</h2>
                                            <p>{{$itemChecklist->checklist->nome}}</p>
                                        </div>
                                        <div class="botoesItens">
                                            @component('components.pagina_erros.tem-erro', ['tem_erro' => $tem_erro, 'itemChecklist' => $itemChecklist, 'avaliacao' => $avaliacao,'id_demanda' => $id,'pgs'=>$pgs])
                                            @endcomponent
                                        </div>
                                    </div>
                        
                                    @if(isset($tem_erro["$itemChecklist->id"]))
                                        @if($avaliacao["$itemChecklist->id"] === 3)
                                            <div class="EstadoOitem NaoSeAplica">
                                                <p>Não se aplica</p>
                                            </div>
                                        @elseif($avaliacao["$itemChecklist->id"] === 2)
                                            <div class="EstadoOitem NaoEstaDeAcordo">
                                                <p>Não está de acordo</p>
                                            </div>
                                        @elseif($avaliacao["$itemChecklist->id"] === 1)
                                            <div class="EstadoOitem deAcordo">
                                                <p>Está de acordo</p>
                                            </div>
                                        @endif
                                    @else
                                    <div class="EstadoOitem">NÃO AVALIADO</div>
                                    @endif

                                    @component('components.pagina_erros.erro-preview', ['tem_erro' => $tem_erro, 'itemChecklist' => $itemChecklist,'pgs'=>$pgs])
                                    @endcomponent
                                </div>    
                                
                                
                                @endif
                            @endforeach
                        @endforeach
                    </div>
                </div>
                    
                @else
                        <div class="conteudoDiretriz" id="conteudo{{ $categoria->id }}">
                            @foreach($categoria->itens as $itemChecklist)
                                @if($tipo === '4' or (!isset($avaliacao["$itemChecklist->id"]) and $tipo == 5) or (isset($avaliacao["$itemChecklist->id"]) and $avaliacao["$itemChecklist->id"] == $tipo) or $opcaoEscolhida == NULL)
                                <div class="oitem itemChecklist">
                                    <div class="oitemConteudo">
                                        <div class="informacoesDoItem">
                                            <h1>ITEM</h1>
                                            <p class="descricaoItem">{{$itemChecklist->descricao}}</p>
                                            <h2>CRITÉRIO(S) WCAG</h2>
                                            @foreach ($itemChecklist->criterios as $criterio)
                                            <p>{{$criterio->codigo}} ({{$criterio->conformidade}}): {{$criterio->nome}}</p>
                                            @endforeach
                                            <h2>Checklist ABNT:</h2>
                                            <p>{{$itemChecklist->checklist->nome}}</p>
                                        </div>
                                        <div class="botoesItens">
                                            @component('components.pagina_erros.tem-erro', ['tem_erro' => $tem_erro, 'itemChecklist' => $itemChecklist, 'avaliacao' => $avaliacao,'id_demanda' => $id,'pgs'=>$pgs])
                                            @endcomponent
                                        </div>
                                    </div>
                        
                                    @if(isset($tem_erro["$itemChecklist->id"]))
                                        @if($avaliacao["$itemChecklist->id"] === 3)
                                            <div class="EstadoOitem NaoSeAplica">
                                                <p>Não se aplica</p>
                                            </div>
                                        @elseif($avaliacao["$itemChecklist->id"] === 2)
                                            <div class="EstadoOitem NaoEstaDeAcordo">
                                                <p>Não está de acordo</p>
                                            </div>
                                        @elseif($avaliacao["$itemChecklist->id"] === 1)
                                            <div class="EstadoOitem deAcordo">
                                                <p>Está de acordo</p>
                                            </div>
                                        @endif
                                    @else
                                    <div class="EstadoOitem">NÃO AVALIADO</div>
                                    @endif

                                    @component('components.pagina_erros.erro-preview', ['tem_erro' => $tem_erro, 'itemChecklist' => $itemChecklist,'pgs'=>$pgs])
                                    @endcomponent
                                </div>    
                                    @endif
                            @endforeach
                        </div>
                    </div>
                    
                @endif
        @endforeach
        </div>

</body>

</html>