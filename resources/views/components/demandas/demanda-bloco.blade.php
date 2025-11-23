@props(['demanda'])

<div class="demanda" data-cy="demanda_teste">
    <div class="oquetem">
        @if($demanda->guideliness == 1)
                    <div class="tem_guide">GUIDELINES</div>
        @endif
        @if($demanda->testeUsuario == 1)
                    <div class="tem_guide">TESTE COM USUÁRIO</div>
        @endif
    </div>
    <div class="information">
        <div class="row">
            <p>Nome da avaliação</p>
            <p>Status</p>
            <p>Ultima Modificação</p>
        </div>
        <hr class="linha">
        <div class="row">
            <p>{{$demanda->nome}}</p>
            <p>{{$demanda->status}}</p>
            <p>16/10/24 às 12:57</p>
        </div>
    </div>
    <hr class="linha">
    <div class="form_senha">
        <form id="formulario{{ $demanda->id }}" method="POST" action="{{ route('demandas.entrar') }}">
            @csrf
            @method('POST')
            <input type="hidden" name="id" value="{{ $demanda->id }}">
            <input type="hidden" name="temguideliness" id="temguideliness{{ $demanda->id }}" value="{{ $demanda->guideliness }}">
            <input type="hidden" name="temtesteUsuario" id="temtesteUsuario{{ $demanda->id }}" value="{{ $demanda->testeUsuario }}">
            <input type="hidden" name="testes" id="ondeir{{ $demanda->id }}">
            <label for="password" class="label_senha">SENHA:</label>
            <input type="password" name="password" class="input_password" data-cy="senha_demanda">
            <button class="button" data-id ={{ $demanda->id }} id = "botaoEntrarDemanda" data-cy="butao_demanda">ENTRAR</button>

            <button type="button" 
                    onclick="window.location.href='{{ route('demandas.gerarRelatorio', ['id' => $demanda->id]) }}'"
                    class="button" 
                    data-cy="botao_relatorio_{{ $demanda->id }}">
                DETALHES
            </button>
        </form>
    </div>
</div>