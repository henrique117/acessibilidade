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
use Illuminate\Support\Str;

class RelatorioController extends Controller
{
    private function prepararDadosRelatorio(Request $request)
    {
        $demandaId = $request->cookie('demanda_authenticated') ?? $request->input('demanda_id');
        $demanda = Demandas::find($demandaId);

        if (!$demanda) return null;

        $usuario = Auth::user();
        
        $erros = Erro::where('avaliacao_id', $demandaId)->get();
        $isRelatorioCompleto = $request->input('relatorio_completo');
        $paginasSelecionadas = $request->input('paginas', []); 
        $todasPaginas = $demanda->paginas;

        $relatorioPorPagina = [];
        $estatisticasGlobais = [
            'total_erros' => 0,
            'por_criticidade' => ['Alta' => 0, 'Media' => 0, 'Baixa' => 0]
        ];

        if ($todasPaginas) {
            foreach ($todasPaginas as $index => $paginaObj) {
                
                if ($isRelatorioCompleto || in_array($index, $paginasSelecionadas)) {
                    
                    $errosDestaPagina = [];
                    $statsPagina = ['Alta' => 0, 'Media' => 0, 'Baixa' => 0, 'Total' => 0];

                    foreach ($erros as $erro) {
                        $afetaEstaPagina = false;
                        
                        if (is_string($erro->pgs)) {
                            $afetaEstaPagina = strpos($erro->pgs, (string)$index) !== false;
                        } elseif (is_array($erro->pgs)) {
                            $afetaEstaPagina = in_array($index, $erro->pgs);
                        }

                        if ($afetaEstaPagina) {
                            $erro->item = Item::find($erro->id_item); 
                            $erro->images = Image::where('id_erro', $erro->id)->get();
                            
                            $errosDestaPagina[] = $erro;

                            $estatisticasGlobais['total_erros']++;
                            
                            if ($erro->em_cfmd == 2) {
                                $statsPagina['Alta']++;
                                $estatisticasGlobais['por_criticidade']['Alta']++;
                            } elseif ($erro->em_cfmd == 3) {
                                $statsPagina['Media']++;
                                $estatisticasGlobais['por_criticidade']['Media']++;
                            } else {
                                $statsPagina['Baixa']++;
                                $estatisticasGlobais['por_criticidade']['Baixa']++;
                            }
                            $statsPagina['Total']++;
                        }
                    }

                    $paginaInfo = is_array($paginaObj) ? $paginaObj : $paginaObj->toArray();

                    $relatorioPorPagina[] = [
                        'info' => $paginaInfo,
                        'erros' => $errosDestaPagina,
                        'stats' => $statsPagina
                    ];
                }
            }
        }

        return [
            'demanda' => $demanda,
            'usuario' => $usuario,
            'relatorioPorPagina' => $relatorioPorPagina,
            'estatisticas' => $estatisticasGlobais,
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