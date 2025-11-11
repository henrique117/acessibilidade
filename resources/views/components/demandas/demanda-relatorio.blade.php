@props(['demanda'])

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerador de Relatório</title>
    @vite( ['resources/css/demanda_relatorio.css','resources/js/demanda_relatorio.js'] )
</head>
<body>

    <h2>Gerador de Relatório: {{ $demanda->nome }}</h2>
    <p>Selecione as opções que deseja incluir no seu relatório:</p>

    <form action="/gerar-relatorio" method="POST">
        @csrf

        <fieldset>
            <legend>Opções de Conteúdo</legend>

            <div class="checkbox-group">
                <input type="checkbox" id="relatorio_completo" name="relatorio_completo" value="1">
                <label for="relatorio_completo">Relatório Completo (Inclui todas as páginas)</label>
            </div>
            
            <hr>

            @foreach($demanda->paginas as $index => $paginaInfo)
                <div class="checkbox-group">
                    <input type="checkbox" id="pagina_{{ $index }}" name="paginas[]" value="{{ $paginaInfo['pagina'] }}" class="checkbox-conteudo">
                    <label for="pagina_{{ $index }}">{{ $paginaInfo['url'] }}</label>
                </div>
            @endforeach

        </fieldset>

        <fieldset>
            <legend>Opções de Formato</legend>
            
            <div class="checkbox-group">
                <input type="checkbox" id="exportar_abnt" name="formato_abnt" value="1">
                <label for="exportar_pdf">Exportar com padrão ABNT</label>
            </div>

            <div class="checkbox-group">
                <input type="checkbox" id="exportar_wcag" name="formato_wcag" value="1">
                <label for="enviar_email">Exportar com padrão WCAG</label>
            </div>
        </fieldset>

        <br>

        <button type="submit">Gerar Relatório</button>

    </form>
</body>
</html>