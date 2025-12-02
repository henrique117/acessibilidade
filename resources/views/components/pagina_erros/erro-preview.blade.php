@props(['itemChecklist','tem_erro','pgs'])

        @if(isset($tem_erro["$itemChecklist->id"]) and $tem_erro["$itemChecklist->id"]->em_cfmd == "2")        
        <div class="menuSuspenso queNaoEstaDeAcordo">
                <div class="barra barraqueNaoEstaDeAcordo">
                    <p>INFORMAÇÕES DO PROBLEMA</p>
                    <img src="img/iconeSuspenso.png" alt="">
                </div>

                <div class="conteudoDiretriz oculto">

                <div class="InformacoesDoProblemaDoItem">
                    <h1>DESCRIÇÃO</h1>

                    <p class="descri">{!! $tem_erro["$itemChecklist->id"]->descricao !!}</p>
                    
                    <h1>PÁGINAS</h1>
                    @foreach ($pgs["$itemChecklist->id"] as $paginas)
                    <p class="pe">{{$paginas['url']}} - {{$paginas['pagina']}}</p>
                    @endforeach
                    <div class="espacoImagens">IMAGENS</div>
                    @foreach ($tem_erro["$itemChecklist->id"]->images as $imagem)
                    <img src="{{asset($imagem->path_image)}}" class="imagens">
                    @endforeach
                    @if(count($tem_erro["$itemChecklist->id"]->images) == 0 )
                     Sem imagens
                    @endif 

                </div>
                
            </div>
            </div>
            @endif