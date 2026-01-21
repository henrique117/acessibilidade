<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Checklist;
use App\Models\Diretriz;
use App\Models\Item;
use App\Models\Erro;
use App\Models\Image;
use App\Models\Demandas;
use Illuminate\Support\Facades\Auth;
use Spatie\Browsershot\Browsershot;

class RelatorioController extends Controller
{
    /**
     * Função Centralizada: Prepara dados tanto para a Tela (index) quanto para o PDF (gerarPdf)
     */
    private function prepararDadosRelatorio(Request $request)
    {
        $demandaId = $request->cookie('demanda_authenticated') ?? $request->input('demanda_id');
        $demanda = Demandas::find($demandaId);

        if (!$demanda) return null;

        $usuario = Auth::user();

        // 1. DADOS COMUNS (Erros e Páginas)
        // Carregamos item, critérios do item e imagens dos erros
        // Importante: 'item.criterios' para poder calcular A/AA/AAA
        $erros = Erro::with(['item.criterios', 'images'])->where('avaliacao_id', $demandaId)->get();
        
        $paginasDemanda = $demanda->paginas;
        if (is_string($paginasDemanda)) $paginasDemanda = json_decode($paginasDemanda, true);
        if (!is_array($paginasDemanda)) $paginasDemanda = [];

        // Inputs da tela
        $search = $request->input('search');
        $opcaoEscolhida = $request->input('diretriz', 'wcag'); 
        $tipo = $request->input('tipo', '4');
        $isRelatorioCompleto = $request->input('relatorio_completo');
        $paginasSelecionadas = $request->input('paginas', []); 

        // 2. RECUPERAÇÃO DE CATEGORIAS (ESSENCIAL PARA A VIEW WELCOME)
        $categorias = [];
        
        if ($opcaoEscolhida === 'wcag') {
            $categorias = Diretriz::with([
                'criterios.itens.checklist', 
                'criterios.itens.criterios' 
            ])->get();
        } else {
            $categorias = Checklist::with([
                'itens.criterios', 
                'itens.checklist'
            ])->get();
        }

        // 3. MAPEAMENTO DE ERROS POR ITEM (PARA A VIEW WELCOME)
        $tem_erro_map = [];
        $avaliacao_map = [];
        $pgs_map = [];

        foreach ($erros as $erro) {
            $tem_erro_map[$erro->id_item] = $erro;
            $avaliacao_map[$erro->id_item] = $erro->em_cfmd;

            // Decodifica páginas deste erro
            $indicesErro = [];
            $pgsString = (string)$erro->pgs;
            if (strpos($pgsString, ',') !== false) {
                $indicesErro = explode(',', $pgsString);
            } else {
                $indicesErro = str_split($pgsString); 
            }
            
            $paginasDetalhadas = [];
            foreach($indicesErro as $idx) {
                if(isset($paginasDemanda[$idx])) {
                    $pagObj = $paginasDemanda[$idx];
                    $paginasDetalhadas[] = is_array($pagObj) ? $pagObj : (array)$pagObj;
                }
            }
            $pgs_map[$erro->id_item] = $paginasDetalhadas;
        }

        // 4. PREPARAÇÃO DOS DADOS PARA O PDF (PÁGINA -> ERROS & ESTATÍSTICAS)
        $relatorioPorPagina = [];
        $statsErros = [];
        $totalTelasAnalizadas = 0;
        $totalDefeitosGeral = 0;

        // Contadores para Níveis de Conformidade
        $qtdA = 0;
        $qtdAA = 0;
        $qtdAAA = 0;

        foreach ($paginasDemanda as $index => $paginaObj) {
            $indexStr = (string)$index;
            
            // Filtro: Relatório Completo OU Página Selecionada
            if ($isRelatorioCompleto || in_array($indexStr, $paginasSelecionadas)) {
                
                $totalTelasAnalizadas++;
                $errosDestaPagina = [];

                foreach ($erros as $erro) {
                    $afetaEstaPagina = false;
                    $pgsString = (string)$erro->pgs;

                    if (strpos($pgsString, ',') !== false) {
                        $indicesArray = explode(',', $pgsString);
                        $afetaEstaPagina = in_array($indexStr, $indicesArray);
                    } else {
                        $afetaEstaPagina = strpos($pgsString, $indexStr) !== false;
                    }

                    if ($afetaEstaPagina) {
                        $errosDestaPagina[] = $erro;
                        $totalDefeitosGeral++;

                        // --- LÓGICA DE CONFORMIDADE WCAG ---
                        // Pega os níveis dos critérios associados ao item deste erro
                        $niveis = [];
                        if ($erro->item && $erro->item->criterios) {
                            $niveis = $erro->item->criterios->pluck('conformidade')->map(function($v) {
                                return strtoupper($v);
                            })->toArray();
                        }

                        // Prioridade: Se tiver critério A, conta como A. Se não, AA. Se não, AAA.
                        if (in_array('A', $niveis)) {
                            $qtdA++;
                        } elseif (in_array('AA', $niveis)) {
                            $qtdAA++;
                        } elseif (in_array('AAA', $niveis)) {
                            $qtdAAA++;
                        }
                        // -----------------------------------

                        // Coleta dados para estatísticas de recorrência
                        $idItem = $erro->id_item;
                        $nomeItem = $erro->item ? $erro->item->descricao : ($erro->titulo ?? 'Item #'.$idItem);

                        if (!isset($statsErros[$idItem])) {
                            $statsErros[$idItem] = ['nome' => $nomeItem, 'ocorrencias' => 0];
                        }
                        $statsErros[$idItem]['ocorrencias']++;
                    }
                }

                $paginaInfo = is_array($paginaObj) ? $paginaObj : (array)$paginaObj;

                $relatorioPorPagina[] = [
                    'info' => $paginaInfo,
                    'erros' => $errosDestaPagina,
                    'total_erros_pagina' => count($errosDestaPagina)
                ];
            }
        }

        // Cálculo das Estatísticas Finais (Únicos vs Recorrentes)
        $totalDefeitosUnicos = 0;
        $totalDefeitosRecorrentes = 0;

        uasort($statsErros, function ($a, $b) {
            return $b['ocorrencias'] <=> $a['ocorrencias'];
        });

        foreach ($statsErros as $stat) {
            if ($stat['ocorrencias'] === 1) {
                $totalDefeitosUnicos++;
            } else {
                $totalDefeitosRecorrentes += $stat['ocorrencias'];
            }
        }

        // Cálculo de Porcentagens de Recorrência
        $percUnicos = $totalDefeitosGeral > 0 ? ($totalDefeitosUnicos / $totalDefeitosGeral) * 100 : 0;
        $percRecorrentes = $totalDefeitosGeral > 0 ? ($totalDefeitosRecorrentes / $totalDefeitosGeral) * 100 : 0;
        
        // Cálculo de Porcentagens de Conformidade
        $percA = $totalDefeitosGeral > 0 ? ($qtdA / $totalDefeitosGeral) * 100 : 0;
        $percAA = $totalDefeitosGeral > 0 ? ($qtdAA / $totalDefeitosGeral) * 100 : 0;
        $percAAA = $totalDefeitosGeral > 0 ? ($qtdAAA / $totalDefeitosGeral) * 100 : 0;

        $ranking = array_values($statsErros);

        $estatisticasDetalhadas = [
            'total_telas' => $totalTelasAnalizadas,
            'total_defeitos' => $totalDefeitosGeral,
            
            // Recorrência
            'total_unicos' => $totalDefeitosUnicos,
            'perc_unicos' => number_format($percUnicos, 2, ',', '.') . '%',
            'total_recorrentes' => $totalDefeitosRecorrentes,
            'perc_recorrentes' => number_format($percRecorrentes, 2, ',', '.') . '%',

            // Conformidade WCAG
            'perc_A' => number_format($percA, 2, ',', '.') . '%',
            'perc_AA' => number_format($percAA, 2, ',', '.') . '%',
            'perc_AAA' => number_format($percAAA, 2, ',', '.') . '%',
            
            // Ranking
            'top_1_nome' => $ranking[0]['nome'] ?? 'Nenhum',
            'top_1_qtd' => $ranking[0]['ocorrencias'] ?? 0,
            'top_2_nome' => $ranking[1]['nome'] ?? 'Nenhum',
            'top_2_qtd' => $ranking[1]['ocorrencias'] ?? 0,
            'top_3_nome' => $ranking[2]['nome'] ?? 'Nenhum',
            'top_3_qtd' => $ranking[2]['ocorrencias'] ?? 0,
        ];

        return [
            'demanda' => $demanda,
            'id' => $demanda->id,
            'usuario' => $usuario,
            'categorias' => $categorias,
            'opcaoEscolhida' => $opcaoEscolhida,
            'tipo' => $tipo,
            'tem_erro' => $tem_erro_map,
            'avaliacao' => $avaliacao_map,
            'pgs' => $pgs_map,
            'search' => $search,
            'itensBusca' => null,
            'relatorioPorPagina' => $relatorioPorPagina,
            'estatisticas' => $estatisticasDetalhadas,
        ];
    }

    public function index(Request $request)
    {
        $dados = $this->prepararDadosRelatorio($request);
        if (!$dados) return redirect()->route('demanda.mostrar');
        
        return view('welcome', $dados);
    }

    public function gerarPdf(Request $request)
    {
        set_time_limit(300);
        $dados = $this->prepararDadosRelatorio($request);

        if (!$dados) return redirect()->back()->with('error', 'Erro ao processar dados.');

        try {
            $html = view('components.relatorio', $dados)->render();
            
            $pdf = Browsershot::html($html)
                ->format('A4')
                ->margins(15, 15, 15, 15)
                ->showBackground()
                ->waitUntilNetworkIdle()
                ->pdf();

            return response($pdf)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="relatorio-acessibilidade.pdf"');

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}