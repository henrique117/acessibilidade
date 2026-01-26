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
    private function prepararDadosRelatorio(Request $request)
    {
        $demandaId = $request->cookie('demanda_authenticated') ?? $request->input('demanda_id');
        $demanda = Demandas::find($demandaId);

        if (!$demanda) return null;

        $usuario = Auth::user();

        // Carrega erros, itens e criterios
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

        // Recuperação de Categorias para a View Welcome
        $categorias = [];
        if ($opcaoEscolhida === 'wcag') {
            $categorias = Diretriz::with(['criterios.itens.checklist', 'criterios.itens.criterios'])->get();
        } else {
            $categorias = Checklist::with(['itens.criterios', 'itens.checklist'])->get();
        }

        // Mapeamento para View Welcome
        $tem_erro_map = [];
        $avaliacao_map = [];
        $pgs_map = [];

        foreach ($erros as $erro) {
            $tem_erro_map[$erro->id_item] = $erro;
            $avaliacao_map[$erro->id_item] = $erro->em_cfmd;

            // Decodifica páginas
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

        // --- PREPARAÇÃO DO RELATÓRIO PDF ---
        $relatorioPorPagina = [];
        $statsErros = [];
        $totalTelasAnalizadas = 0;
        $totalDefeitosGeral = 0;

        $qtdA = 0; $qtdAA = 0; $qtdAAA = 0;
        $principio1 = 0;
        $principio2 = 0;
        $principio3 = 0;
        $principio4 = 0;

        foreach ($paginasDemanda as $index => $paginaObj) {
            $indexStr = (string)$index;
            
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

                        // --- ESTATÍSTICAS WCAG (Nível e Princípio) ---
                        $niveis = [];
                        $codigos = [];

                        if ($erro->item && $erro->item->criterios) {
                            foreach($erro->item->criterios as $crit) {
                                $niveis[] = strtoupper($crit->conformidade);
                                $codigos[] = $crit->codigo; // Ex: "1.1.1"
                            }
                        }

                        // Contagem A/AA/AAA (Prioridade A > AA > AAA)
                        if (in_array('A', $niveis)) $qtdA++;
                        elseif (in_array('AA', $niveis)) $qtdAA++;
                        elseif (in_array('AAA', $niveis)) $qtdAAA++;

                        // Contagem Princípios
                        $p1_counted = false; $p2_counted = false; $p3_counted = false; $p4_counted = false;

                        foreach($codigos as $cod) {
                            $primeiroDigito = substr($cod, 0, 1);
                            
                            if ($primeiroDigito === '1' && !$p1_counted) { $principio1++; $p1_counted = true; }
                            if ($primeiroDigito === '2' && !$p2_counted) { $principio2++; $p2_counted = true; }
                            if ($primeiroDigito === '3' && !$p3_counted) { $principio3++; $p3_counted = true; }
                            if ($primeiroDigito === '4' && !$p4_counted) { $principio4++; $p4_counted = true; }
                        }

                        // Recorrência
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

        // Cálculos Finais de Porcentagem
        $totalDefeitosUnicos = 0;
        $totalDefeitosRecorrentes = 0;

        uasort($statsErros, function ($a, $b) { return $b['ocorrencias'] <=> $a['ocorrencias']; });

        foreach ($statsErros as $stat) {
            if ($stat['ocorrencias'] === 1) $totalDefeitosUnicos++;
            else $totalDefeitosRecorrentes += $stat['ocorrencias'];
        }

        $calcPerc = function($qtd) use ($totalDefeitosGeral) {
            return $totalDefeitosGeral > 0 ? number_format(($qtd / $totalDefeitosGeral) * 100, 2, ',', '.') . '%' : '0,00%';
        };

        $ranking = array_values($statsErros);

        $estatisticasDetalhadas = [
            'total_telas' => $totalTelasAnalizadas,
            'total_defeitos' => $totalDefeitosGeral,
            
            // Recorrência
            'total_unicos' => $totalDefeitosUnicos,
            'perc_unicos' => $calcPerc($totalDefeitosUnicos),
            'total_recorrentes' => $totalDefeitosRecorrentes,
            'perc_recorrentes' => $calcPerc($totalDefeitosRecorrentes),

            // Níveis WCAG
            'perc_A' => $calcPerc($qtdA),
            'perc_AA' => $calcPerc($qtdAA),
            'perc_AAA' => $calcPerc($qtdAAA),

            // Princípios WCAG
            'perc_p1' => $calcPerc($principio1),
            'perc_p2' => $calcPerc($principio2),
            'perc_p3' => $calcPerc($principio3),
            'perc_p4' => $calcPerc($principio4),
            
            // Ranking
            'top_1_nome' => $ranking[0]['nome'] ?? 'Nenhum', 'top_1_qtd' => $ranking[0]['ocorrencias'] ?? 0,
            'top_2_nome' => $ranking[1]['nome'] ?? 'Nenhum', 'top_2_qtd' => $ranking[1]['ocorrencias'] ?? 0,
            'top_3_nome' => $ranking[2]['nome'] ?? 'Nenhum', 'top_3_qtd' => $ranking[2]['ocorrencias'] ?? 0,
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