<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

        $erros = Erro::with('item')->where('avaliacao_id', $demandaId)->get();
        
        $isRelatorioCompleto = $request->input('relatorio_completo');
        $paginasSelecionadas = $request->input('paginas', []); 

        $todasPaginas = $demanda->paginas;
        if (is_string($todasPaginas)) {
            $todasPaginas = json_decode($todasPaginas, true);
        }
        if (!is_array($todasPaginas)) {
            $todasPaginas = [];
        }

        $relatorioPorPagina = [];
        
        $statsErros = [];

        $totalTelasAnalizadas = 0;
        $totalDefeitosGeral = 0;

        foreach ($todasPaginas as $index => $paginaObj) {
            
            if ($isRelatorioCompleto || in_array((string)$index, $paginasSelecionadas) || in_array($index, $paginasSelecionadas)) {
                
                $totalTelasAnalizadas++;
                $errosDestaPagina = [];

                foreach ($erros as $erro) {
                    $afetaEstaPagina = false;

                    $pgsString = (string)$erro->pgs;
                    $indiceProcurado = (string)$index;

                    if (strpos($pgsString, ',') !== false) {
                        $indicesArray = explode(',', $pgsString);
                        $afetaEstaPagina = in_array($indiceProcurado, $indicesArray);
                    } else {
                        $afetaEstaPagina = strpos($pgsString, $indiceProcurado) !== false;
                    }

                    if ($afetaEstaPagina) {
                        $erro->images = Image::where('id_erro', $erro->id)->get();
                        
                        $errosDestaPagina[] = $erro;
                        $totalDefeitosGeral++;

                        $idItem = $erro->id_item;
                        $nomeItem = $erro->item ? $erro->item->descricao : ($erro->titulo ?? 'Item #'.$idItem);

                        if (!isset($statsErros[$idItem])) {
                            $statsErros[$idItem] = [
                                'nome' => $nomeItem,
                                'ocorrencias' => 0
                            ];
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

        $percUnicos = $totalDefeitosGeral > 0 ? ($totalDefeitosUnicos / $totalDefeitosGeral) * 100 : 0;
        $percRecorrentes = $totalDefeitosGeral > 0 ? ($totalDefeitosRecorrentes / $totalDefeitosGeral) * 100 : 0;

        $ranking = array_values($statsErros);

        $estatisticasDetalhadas = [
            'total_telas' => $totalTelasAnalizadas,
            'total_defeitos' => $totalDefeitosGeral,
            'total_unicos' => $totalDefeitosUnicos,
            'perc_unicos' => number_format($percUnicos, 1, ',', '.') . '%',
            'total_recorrentes' => $totalDefeitosRecorrentes,
            'perc_recorrentes' => number_format($percRecorrentes, 1, ',', '.') . '%',
            
            'top_1_nome' => $ranking[0]['nome'] ?? 'Nenhum',
            'top_1_qtd' => $ranking[0]['ocorrencias'] ?? 0,
            
            'top_2_nome' => $ranking[1]['nome'] ?? 'Nenhum',
            'top_2_qtd' => $ranking[1]['ocorrencias'] ?? 0,
            
            'top_3_nome' => $ranking[2]['nome'] ?? 'Nenhum',
            'top_3_qtd' => $ranking[2]['ocorrencias'] ?? 0,
        ];

        return [
            'demanda' => $demanda,
            'usuario' => $usuario,
            'relatorioPorPagina' => $relatorioPorPagina,
            'estatisticas' => $estatisticasDetalhadas,
            'filtros' => [
                'tipo' => $request->tipo,
                'wcag' => $request->diretriz
            ]
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

        if (!$dados) {
            return redirect()->back()->with('error', 'Erro ao processar dados.');
        }

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