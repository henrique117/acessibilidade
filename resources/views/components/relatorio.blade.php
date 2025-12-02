<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório - {{ $demanda->nome }}</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; font-size: 11pt; line-height: 1.5; }
        h1, h2, h3 { color: #2c3e50; }
        .page-break { page-break-after: always; }
        a { text-decoration: none; color: inherit; }
        
        .capa { text-align: center; padding-top: 200px; page-break-after: always; }
        .capa h1 { font-size: 26pt; margin-bottom: 10px; }
        .capa h2 { font-size: 18pt; color: #7f8c8d; font-weight: normal; margin-bottom: 50px; }
        .info-box { border: 1px solid #ddd; padding: 20px; display: inline-block; text-align: left; background: #f9f9f9; border-radius: 8px; min-width: 350px; }
        
        .sumario-container { padding: 40px; page-break-after: always; }
        .sumario-title { text-align: center; border-bottom: 2px solid #2c3e50; padding-bottom: 10px; margin-bottom: 30px; }
        
        .toc-list { list-style: none; padding: 0; }
        
        .toc-item { margin-bottom: 15px; }
        
        .toc-main-link-wrapper { 
            border-bottom: 1px dotted #ccc; 
            margin-bottom: 5px; 
            padding-bottom: 2px;
            display: flex; 
            justify-content: space-between;
        }

        .toc-link { 
            font-weight: bold; 
            color: #2c3e50; 
            text-decoration: none; 
            width: 100%;
            display: block;
        }
        
        .toc-sublist { 
            list-style: none; 
            padding-left: 25px;
            margin-top: 5px; 
        }
        
        .toc-subitem { 
            margin-bottom: 8px; 
            border-bottom: 1px dotted #eee; 
            display: flex; 
            justify-content: space-between;
        }
        
        .toc-sublink { 
            font-size: 0.95em; 
            color: #2c3e50;
            text-decoration: none; 
            width: 100%;
            display: block;
            padding-bottom: 2px;
        }
        
        .toc-sublink:hover, .toc-link:hover { color: #2980b9; }

        .intro-container { padding: 40px; text-align: justify; page-break-after: always; }
        .intro-text p { margin-bottom: 15px; text-indent: 30px; }
        
        .section-header { border-bottom: 2px solid #2c3e50; padding-bottom: 10px; margin-bottom: 20px; margin-top: 30px; }
        .dashboard-container { display: flex; justify-content: space-around; align-items: center; margin-bottom: 30px; }
        .chart-wrapper { width: 45%; max-height: 300px; text-align: center; }
        .big-number { font-size: 4em; font-weight: bold; color: #2c3e50; display: block; }

        .tabela-resumo { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 0.9em; page-break-inside: avoid; }
        .tabela-resumo th { background-color: #34495e; color: white; padding: 10px; }
        .tabela-resumo td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        .col-pag { text-align: left !important; }
        .bg-red-light { background-color: #fadbd8; color: #c0392b; font-weight: bold; }
        .bg-orange-light { background-color: #fdebd0; color: #d35400; }
        .bg-total { background-color: #ecf0f1; font-weight: bold; }

        .page-header { 
            background: #2c3e50; color: #fff; padding: 15px; 
            font-size: 14pt; margin-top: 40px; margin-bottom: 20px;
            border-left: 8px solid #3498db; 
        }
        .erro-card { border: 1px solid #e0e0e0; margin-bottom: 20px; padding: 20px; background: #fff; page-break-inside: avoid; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .erro-top { display: flex; justify-content: space-between; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 10px; }
        .badge { padding: 5px 10px; border-radius: 4px; color: white; font-size: 0.8em; font-weight: bold; text-transform: uppercase; }
        .bg-alta { background-color: #e74c3c; }
        .bg-media { background-color: #f39c12; }
        .bg-baixa { background-color: #27ae60; }
        .desc-tecnica { background: #fcfcfc; padding: 10px; border-left: 3px solid #ddd; font-size: 0.95em; }
        .evidence-img { max-width: 100%; max-height: 300px; border: 1px solid #999; margin: 5px 0; }
    </style>
</head>

<body>
    <div class="capa">
        <img src="{{ public_path('img/logo.png') }}" style="max-width: 180px; margin-bottom: 30px;" onerror="this.style.display='none'">
        <h1>Relatório de Conformidade de Acessibilidade Digital</h1>
        <h2>Aplicativo: {{ $demanda->nome }}</h2>
        
        <div class="info-box">
            {{-- CORREÇÃO DA DATA: Forçando o Timezone para São Paulo --}}
            <p><strong>Data do Relatório:</strong> {{ \Carbon\Carbon::now('America/Sao_Paulo')->format('d/m/Y') }}</p>
            <p><strong>Produto/Versão:</strong> {{ $demanda->nome }}</p>
            <p><strong>Especialista:</strong> {{ $usuario->name ?? 'Equipe de Acessibilidade' }}</p>
            <p><strong>Diretrizes:</strong> WCAG 2.1 / 2.2 (Níveis A e AA)</p>
        </div>
    </div>

    <div class="sumario-container">
        <h2 class="sumario-title">SUMÁRIO</h2>
        <ul class="toc-list">
            <li class="toc-item">
                <div class="toc-main-link-wrapper">
                    <a href="#introducao" class="toc-link">1. Introdução</a>
                </div>
            </li>
            
            <li class="toc-item">
                <div class="toc-main-link-wrapper">
                    <a href="#visao-geral" class="toc-link">2. Visão Geral da Análise</a>
                </div>
            </li>
            
            <li class="toc-item">
                <div class="toc-main-link-wrapper">
                    <span class="toc-link">3. Páginas Analisadas</span>
                </div>
                
                <ul class="toc-sublist">
                    @foreach($relatorioPorPagina as $pag)
                        <li class="toc-subitem">
                            <a href="#pag-{{ $loop->iteration }}" class="toc-sublink">
                                3.{{ $loop->iteration }}. {{ \Illuminate\Support\Str::limit($pag['info']['url'] ?? 'Página ' . $loop->iteration, 60) }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>
        </ul>
    </div>

    <div class="intro-container" id="introducao">
        <h2 class="section-header">1. Introdução</h2>
        
        <div class="intro-text">
            <p>
                Em uma era onde a digitalização permeia cada aspecto de nossa vida cotidiana, a responsabilidade de criar ambientes digitais inclusivos torna-se mais crucial do que nunca. A acessibilidade digital não é apenas uma questão ética, mas um compromisso com o ideal de que todos, independentemente de quaisquer limitações físicas, cognitivas ou sensoriais, devem ter acesso igualitário às plataformas e serviços digitais.
            </p>
            <p>
                Nesse contexto, a equipe de especialistas conduziu uma avaliação abrangente da acessibilidade digital do aplicativo <strong>{{ $demanda->nome }}</strong>. O propósito desta validação foi identificar e catalogar possíveis barreiras na plataforma que poderiam impactar a experiência de usuários com diversas necessidades.
            </p>
            <p>
                Para a composição deste relatório, adotou-se uma metodologia robusta que incorporou testes visuais e não visuais, fazendo uso de tecnologia assistiva combinados com dispositivos físicos, e seguindo estritamente as Diretrizes de Acessibilidade para o Conteúdo da Web (WCAG). Foi desenvolvido um roteiro de testes abrangente, garantindo uma avaliação completa e precisa dos fluxos analisados.
            </p>
            <p>
                Neste estudo, foram considerados fluxos estratégicos do aplicativo, totalizando <strong>{{ count($relatorioPorPagina) }} telas/páginas analisadas</strong>, conforme detalhado na seção "Páginas Analisadas" deste documento.
            </p>
            <p>
                A análise de conformidade contemplou a execução de testes manuais e automatizados. O produto foi testado visando a conformidade com os critérios de sucesso da WCAG nos níveis A e AA.
            </p>
            <p>
                O objetivo principal deste relatório é apresentar os defeitos de acessibilidade observados durante a análise realizada, e, consequentemente, fornecer subsídios para o aprimoramento do aplicativo. Com a implementação das correções, a plataforma não só reforçará seu compromisso com a inclusão digital, mas também enriquecerá a experiência de todos os seus usuários.
            </p>
        </div>
    </div>

    <div id="visao-geral">
        <h2 class="section-header">2. Visão Geral da Análise</h2>
        
        <div class="dashboard-container">
            <div class="chart-wrapper">
                <canvas id="chartCriticidade"></canvas>
            </div>
            <div class="chart-wrapper">
                <span class="big-number">{{ $estatisticas['total_erros'] }}</span>
                <span class="label">Total de Erros</span>
                <div style="text-align: left; padding-left: 20px; font-size: 0.9em; margin-top: 15px;">
                    <p>🔴 Alta: <strong>{{ $estatisticas['por_criticidade']['Alta'] }}</strong></p>
                    <p>🟠 Média: <strong>{{ $estatisticas['por_criticidade']['Media'] }}</strong></p>
                    <p>🟢 Baixa: <strong>{{ $estatisticas['por_criticidade']['Baixa'] }}</strong></p>
                </div>
            </div>
        </div>

        <h3 style="margin-top: 40px;">Resumo Quantitativo por Página</h3>
        <table class="tabela-resumo">
            <thead>
                <tr>
                    <th class="col-pag">Página / Fluxo</th>
                    <th>Alta</th>
                    <th>Média</th>
                    <th>Baixa</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($relatorioPorPagina as $pag)
                    <tr>
                        <td class="col-pag">
                            <strong>{{ $pag['info']['url'] ?? 'Página ' . $loop->iteration }}</strong><br>
                            <span style="color: #666; font-size: 0.8em;">{{ \Illuminate\Support\Str::limit($pag['info']['pagina'] ?? '', 50) }}</span>
                        </td>
                        <td class="{{ $pag['stats']['Alta'] > 0 ? 'bg-red-light' : '' }}">{{ $pag['stats']['Alta'] }}</td>
                        <td class="{{ $pag['stats']['Media'] > 0 ? 'bg-orange-light' : '' }}">{{ $pag['stats']['Media'] }}</td>
                        <td>{{ $pag['stats']['Baixa'] }}</td>
                        <td class="bg-total">{{ $pag['stats']['Total'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="page-break"></div>

    <div id="paginas-detalhe">
        <h2 class="section-header">3. Páginas Analisadas - Detalhamento Técnico</h2>

        @foreach($relatorioPorPagina as $dadosPagina)
            
            <div id="pag-{{ $loop->iteration }}" style="position: relative; top: -50px;"></div>

            <div class="page-header">
                <div>3.{{ $loop->iteration }} - {{ $dadosPagina['info']['url'] ?? 'Página' }}</div>
                <div style="font-size: 0.6em; font-weight: normal; margin-top: 5px; opacity: 0.9;">
                    URL: {{ $dadosPagina['info']['pagina'] ?? '-' }}
                </div>
            </div>

            @if(count($dadosPagina['erros']) == 0)
                <div style="padding: 20px; background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; border-radius: 5px;">
                    <strong>✅ Conformidade:</strong> Nenhum erro de acessibilidade foi detectado nesta página durante a validação.
                </div>
            @else
                @foreach($dadosPagina['erros'] as $erro)
                    <div class="erro-card">
                        <div class="erro-top">
                            <span class="erro-id">#{{ $erro->id }} - {{ $erro->item->descricao ?? 'Critério WCAG' }}</span>
                            @php
                                $badge = match((string)$erro->em_cfmd) {
                                    '2' => ['cls'=>'bg-alta', 'txt'=>'Não Conforme'],
                                    '3' => ['cls'=>'bg-media', 'txt'=>'Atenção'],
                                    default => ['cls'=>'bg-baixa', 'txt'=>'Info'],
                                };
                            @endphp
                            <span class="badge {{ $badge['cls'] }}">{{ $badge['txt'] }}</span>
                        </div>
                        
                        <span class="label">Descrição do Problema:</span>
                        <div class="desc-tecnica">{!! $erro->descricao !!}</div>

                        @if(count($erro->images) > 0)
                            <div style="margin-top: 15px; padding: 10px; background: #fafafa; border: 1px dashed #ccc;">
                                <span class="label">Evidências:</span>
                                @foreach($erro->images as $img)
                                    <img src="{{ public_path($img->path_image) }}" class="evidence-img">
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            @endif

            <div class="page-break"></div>
        @endforeach
    </div>

    <script>
        const stats = @json($estatisticas);
        
        const ctx = document.getElementById('chartCriticidade').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Alta', 'Média', 'Baixa'],
                datasets: [{
                    data: [stats.por_criticidade.Alta, stats.por_criticidade.Media, stats.por_criticidade.Baixa],
                    backgroundColor: ['#e74c3c', '#f39c12', '#27ae60'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false, 
                plugins: { legend: { position: 'bottom' } }
            }
        });
    </script>
</body>
</html>