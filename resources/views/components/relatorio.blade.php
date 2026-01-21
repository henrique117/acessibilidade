<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório - {{ $demanda->nome }}</title>
    
    <style>
        /* --- CSS GLOBAL --- */
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            color: #333; 
            font-size: 11pt; 
            line-height: 1.5; 
            margin: 0;
            padding: 0;
        }
        h1, h2, h3, h4 { color: #2c3e50; margin-bottom: 10px; margin-top: 0; }
        .page-break { page-break-after: always; }
        a { text-decoration: none; color: inherit; }
        
        /* --- CAPA --- */
        .capa { text-align: center; padding-top: 200px; page-break-after: always; }
        .capa h1 { font-size: 26pt; margin-bottom: 10px; }
        .capa h2 { font-size: 18pt; color: #7f8c8d; font-weight: normal; margin-bottom: 50px; }
        .info-box { 
            border: 1px solid #ddd; 
            padding: 20px; 
            display: inline-block; 
            text-align: left; 
            background: #f9f9f9; 
            border-radius: 8px; 
            min-width: 350px; 
        }
        .info-box p { margin: 8px 0; }
        
        /* --- SUMÁRIO --- */
        .sumario-container { padding: 40px; page-break-after: always; }
        .sumario-title { text-align: center; border-bottom: 2px solid #2c3e50; padding-bottom: 10px; margin-bottom: 30px; }
        .toc-list { list-style: none; padding: 0; }
        .toc-item { margin-bottom: 15px; }
        .toc-main-link-wrapper { border-bottom: 1px dotted #ccc; margin-bottom: 5px; padding-bottom: 2px; }
        .toc-link { font-weight: bold; color: #2c3e50; display: block; }
        .toc-sublist { list-style: none; padding-left: 25px; margin-top: 5px; }
        .toc-subitem { margin-bottom: 8px; border-bottom: 1px dotted #eee; }
        .toc-sublink { font-size: 0.95em; color: #2c3e50; display: block; padding-bottom: 2px; }
        
        /* --- INTRODUÇÃO --- */
        .intro-container { padding: 40px; text-align: justify; page-break-after: always; }
        .intro-text p { margin-bottom: 15px; text-indent: 30px; }
        
        /* --- DASHBOARD NOVA (ESTATÍSTICAS) --- */
        .section-header { border-bottom: 2px solid #2c3e50; padding-bottom: 10px; margin-bottom: 20px; margin-top: 30px; }
        
        .stats-grid { display: flex; justify-content: space-between; margin-bottom: 30px; gap: 20px; }
        .stat-card { 
            flex: 1; 
            background: #f8f9fa; 
            border: 1px solid #e9ecef; 
            padding: 20px; 
            text-align: center; 
            border-radius: 8px;
        }
        .stat-value { font-size: 3em; font-weight: bold; color: #2c3e50; display: block; line-height: 1; margin-bottom: 10px; }
        .stat-label { font-size: 1em; color: #7f8c8d; text-transform: uppercase; letter-spacing: 1px; }

        /* Tabela de Recorrência */
        .tabela-stats { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .tabela-stats th { background: #34495e; color: #fff; padding: 12px; text-align: left; }
        .tabela-stats td { border: 1px solid #ddd; padding: 12px; }
        .tabela-stats tr:nth-child(even) { background: #f9f9f9; }

        /* Ranking Top 3 */
        .ranking-box { background: #fff; border: 1px solid #ddd; border-radius: 8px; overflow: hidden; margin-bottom: 30px; }
        .ranking-item { 
            display: flex; 
            justify-content: space-between; 
            padding: 15px 20px; 
            border-bottom: 1px solid #eee; 
            align-items: center;
        }
        .ranking-item:last-child { border-bottom: none; }
        .rank-pos { 
            background: #e74c3c; color: #fff; 
            width: 30px; height: 30px; 
            border-radius: 50%; 
            display: flex; align-items: center; justify-content: center; 
            font-weight: bold; margin-right: 15px;
            flex-shrink: 0;
        }
        .rank-pos.p2 { background: #e67e22; }
        .rank-pos.p3 { background: #f1c40f; color: #333; }
        .rank-info { flex: 1; padding-right: 10px; }
        .rank-name { font-weight: bold; display: block; color: #2c3e50; }
        .rank-count { font-weight: bold; color: #555; background: #eee; padding: 5px 10px; border-radius: 4px; font-size: 0.9em; white-space: nowrap; }

        /* --- TABELA POR PÁGINA --- */
        .tabela-resumo { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 0.9em; page-break-inside: avoid; }
        .tabela-resumo th { background-color: #34495e; color: white; padding: 10px; }
        .tabela-resumo td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        .col-pag { text-align: left !important; width: 70%; }
        .bg-total { background-color: #ecf0f1; font-weight: bold; }

        /* --- DETALHAMENTO --- */
        .page-header { 
            background: #2c3e50; color: #fff; padding: 15px; 
            font-size: 14pt; margin-top: 40px; margin-bottom: 20px;
            border-left: 8px solid #3498db; 
        }
        .erro-card { border: 1px solid #e0e0e0; margin-bottom: 20px; padding: 20px; background: #fff; page-break-inside: avoid; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .erro-top { display: flex; justify-content: space-between; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 10px; }
        .erro-id { font-weight: bold; color: #555; }
        .label { font-weight: bold; color: #2c3e50; display: block; margin-bottom: 5px; margin-top: 10px; }
        .desc-tecnica { background: #fcfcfc; padding: 10px; border-left: 3px solid #ddd; font-size: 0.95em; }
        .evidence-img { max-width: 100%; max-height: 300px; border: 1px solid #999; margin: 5px 0; }
    </style>
</head>
<body>

    <!-- 1. CAPA -->
    <div class="capa">
        <img src="{{ public_path('img/logo.png') }}" style="max-width: 180px; margin-bottom: 30px;" onerror="this.style.display='none'">
        <h1>Relatório de Conformidade de Acessibilidade Digital</h1>
        <h2>Aplicativo: {{ $demanda->nome }}</h2>
        
        <div class="info-box">
            <p><strong>Data do Relatório:</strong> {{ \Carbon\Carbon::now('America/Sao_Paulo')->format('d/m/Y') }}</p>
            <p><strong>Produto/Versão:</strong> {{ $demanda->nome }}</p>
            <p><strong>Especialista:</strong> {{ $usuario->name ?? 'Equipe de Acessibilidade' }}</p>
            <p><strong>Total de Defeitos:</strong> {{ $estatisticas['total_defeitos'] }}</p>
        </div>
    </div>

    <!-- 2. SUMÁRIO -->
    <div class="sumario-container">
        <h2 class="sumario-title">SUMÁRIO</h2>
        <ul class="toc-list">
            <li class="toc-item">
                <div class="toc-main-link-wrapper"><a href="#introducao" class="toc-link">1. Introdução</a></div>
            </li>
            <li class="toc-item">
                <div class="toc-main-link-wrapper"><a href="#visao-geral" class="toc-link">2. Visão Geral e Estatísticas</a></div>
            </li>
            <li class="toc-item">
                <div class="toc-main-link-wrapper"><span class="toc-link">3. Detalhamento por Página</span></div>
                <ul class="toc-sublist">
                    @foreach($relatorioPorPagina as $pag)
                        <li class="toc-subitem">
                            <a href="#pag-{{ $loop->iteration }}" class="toc-sublink">
                                {{-- REVERTIDO: Voltamos a usar 'url' como título da página, conforme estava antes --}}
                                3.{{ $loop->iteration }} - {{ \Illuminate\Support\Str::limit($pag['info']['url'] ?? 'Página ' . $loop->iteration, 60) }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>
        </ul>
    </div>

    <!-- 3. INTRODUÇÃO -->
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
                Para a composição deste relatório, adotou-se uma metodologia robusta que incorporou testes visuais e não visuais, fazendo uso de tecnologia assistiva combinados com dispositivos físicos, e seguindo estritamente as Diretrizes de Acessibilidade para o Conteúdo da Web (WCAG).
            </p>
            <p>
                Este documento apresenta as estatísticas gerais de conformidade, incluindo a análise de defeitos únicos e recorrentes, além do detalhamento técnico de cada página avaliada.
            </p>
        </div>
    </div>

    <!-- 4. VISÃO GERAL (NOVAS ESTATÍSTICAS) -->
    <div id="visao-geral">
        <h2 class="section-header">2. Visão Geral da Análise</h2>
        
        <!-- Cards Principais -->
        <div class="stats-grid">
            <div class="stat-card">
                <span class="stat-value">{{ $estatisticas['total_telas'] }}</span>
                <span class="stat-label">Telas Analisadas</span>
            </div>
            <div class="stat-card">
                <span class="stat-value">{{ $estatisticas['total_defeitos'] }}</span>
                <span class="stat-label">Total de Defeitos</span>
            </div>
        </div>

        <!-- Tabela de Tipos de Defeito (Recorrência) -->
        <h3>Análise de Recorrência</h3>
        <table class="tabela-stats">
            <thead>
                <tr>
                    <th>Classificação</th>
                    <th>Quantidade</th>
                    <th>Porcentagem do Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>Defeitos Únicos</strong><br>
                        <small style="color:#666">Erros específicos que ocorreram em apenas uma tela</small>
                    </td>
                    <td>{{ $estatisticas['total_unicos'] }}</td>
                    <td>{{ $estatisticas['perc_unicos'] }}</td>
                </tr>
                <tr>
                    <td>
                        <strong>Defeitos Recorrentes</strong><br>
                        <small style="color:#666">Erros repetidos (mesmo padrão) em múltiplas telas</small>
                    </td>
                    <td>{{ $estatisticas['total_recorrentes'] }}</td>
                    <td>{{ $estatisticas['perc_recorrentes'] }}</td>
                </tr>
            </tbody>
        </table>

        <!-- NOVA TABELA: Conformidade WCAG -->
        <h3>Conformidade WCAG</h3>
        <table class="tabela-stats">
            <thead>
                <tr>
                    <th>Nível de Conformidade</th>
                    <th>Porcentagem dos Defeitos</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Porcentagem Conformidade A</strong></td>
                    <td>{{ $estatisticas['perc_A'] }}</td>
                </tr>
                <tr>
                    <td><strong>Porcentagem Conformidade AA</strong></td>
                    <td>{{ $estatisticas['perc_AA'] }}</td>
                </tr>
                <tr>
                    <td><strong>Porcentagem Conformidade AAA</strong></td>
                    <td>{{ $estatisticas['perc_AAA'] }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Ranking Top 3 -->
        <h3>Defeitos Mais Recorrentes</h3>
        <div class="ranking-box">
            @if($estatisticas['top_1_qtd'] > 0)
            <div class="ranking-item">
                <div class="rank-pos">1º</div>
                <div class="rank-info">
                    <span class="rank-name">{{ $estatisticas['top_1_nome'] }}</span>
                </div>
                <div class="rank-count">{{ $estatisticas['top_1_qtd'] }} ocorrências</div>
            </div>
            @endif

            @if($estatisticas['top_2_qtd'] > 0)
            <div class="ranking-item">
                <div class="rank-pos p2">2º</div>
                <div class="rank-info">
                    <span class="rank-name">{{ $estatisticas['top_2_nome'] }}</span>
                </div>
                <div class="rank-count">{{ $estatisticas['top_2_qtd'] }} ocorrências</div>
            </div>
            @endif

            @if($estatisticas['top_3_qtd'] > 0)
            <div class="ranking-item">
                <div class="rank-pos p3">3º</div>
                <div class="rank-info">
                    <span class="rank-name">{{ $estatisticas['top_3_nome'] }}</span>
                </div>
                <div class="rank-count">{{ $estatisticas['top_3_qtd'] }} ocorrências</div>
            </div>
            @endif
            
            @if($estatisticas['top_1_qtd'] == 0)
                <div style="padding: 20px; text-align: center; color: #7f8c8d;">Nenhum defeito recorrente identificado.</div>
            @endif
        </div>

        <!-- Resumo por Página Simplificado -->
        <h3 style="margin-top: 40px;">Resumo por Página</h3>
        <table class="tabela-resumo">
            <thead>
                <tr>
                    <th class="col-pag">Página / Fluxo</th>
                    <th>Total de Erros</th>
                </tr>
            </thead>
            <tbody>
                @foreach($relatorioPorPagina as $pag)
                    <tr>
                        <td class="col-pag">
                            <strong>{{ $pag['info']['url'] ?? 'Página ' . $loop->iteration }}</strong><br>
                            <span style="color: #666; font-size: 0.8em;">{{ \Illuminate\Support\Str::limit($pag['info']['pagina'] ?? '', 50) }}</span>
                        </td>
                        <td class="bg-total">{{ $pag['total_erros_pagina'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="page-break"></div>

    <!-- 5. DETALHAMENTO TÉCNICO -->
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
                    <strong>✅ Conformidade:</strong> Nenhum erro de acessibilidade foi detectado nesta página.
                </div>
            @else
                @foreach($dadosPagina['erros'] as $erro)
                    <div class="erro-card">
                        <div class="erro-top">
                            <span class="erro-id">ID: #{{ $erro->id }}</span>
                        </div>
                        
                        <div style="margin-bottom: 10px; font-weight: bold; color: #2c3e50;">
                             {{ $erro->item->descricao ?? $erro->titulo ?? 'Erro Identificado' }}
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

</body>
</html>