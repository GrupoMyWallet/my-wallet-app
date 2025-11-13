<?php

namespace App\Http\Controllers;

use App\Services\PythonApiService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ImportController extends Controller
{
    protected PythonApiService $pythonApi;

    public function __construct(PythonApiService $pythonApi)
    {
        $this->pythonApi = $pythonApi;
    }

    /**
     * Exibe a tela de importação de lançamentos
     */
    public function index()
    {
        return Inertia::render('Lancamentos/Imports', [
            'title' => 'Importar Lançamentos',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Lançamentos', 'url' => route('lancamentos.index')],
                ['label' => 'Importar', 'url' => null],
            ],
        ]);
    }

    /**
     * Processa a importação de planilhas (CSV/XLSX)
     */
    public function importSpreadsheets(Request $request)
    {
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'file|mimes:csv,xlsx,xls|max:10240', // 10MB max
        ]);

        // TODO: Implementar lógica de importação de planilhas
        // - Processar arquivos CSV/XLSX
        // - Validar estrutura das planilhas
        // - Importar dados para o banco
        // - Retornar resultado da importação

        return redirect()->route('lancamentos.import')->with('error', 'Essa funcionalidade ainda será implementada. Tente novamente em breve.');
    }

    public function importArquivo(Request $request)
    {
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'file|mimes:pdf,csv|max:20480',
            'document_type' => 'required|in:extrato,fatura'
        ]);

        DB::beginTransaction();

        try {
            $resultsForRedirect = [];
            $lancamentosParaCriar = []; // 1. Array para preparar os dados para o createMany

            // Processa cada arquivo
            foreach ($request->file('files') as $file) {
                $apiResult = $this->pythonApi->processarArquivo(
                    $file,
                    ['document_type' => $request->input('document_type')]
                );

                // Itera sobre cada lançamento retornado pela API para o arquivo
                foreach ($apiResult as $lancamentoData) {
                    if (str_contains($lancamentoData['descricao'], 'BB Rende')) {
                        continue;
                    }

                    $data = ($lancamentoData['data'] == null) ? Carbon::now()->format('Y-m-d') : Carbon::createFromFormat('d/m/Y H:i', $lancamentoData['data'])->format('Y-m-d');
                    $valor = $lancamentoData['valor'];
                    if ($valor < 0) {
                        $valor = abs($valor); // Converte para positivo
                    }

                    $lancamentosParaCriar[] = [
                        // 'user_id' não é necessário aqui, pois a relação já o adicionará
                        'tipo' => ($lancamentoData['tipo_lancamento'] == 'Entrada') ? 'receita' : 'despesa',
                        'valor' => $valor,
                        'descricao' => $lancamentoData['descricao'],
                        'categoria_id' => null, 
                        'data' => $data,
                        'esta_ativa' => true,
                    ];
                }
                
                // Mantém o resultado original para enviar no redirect
                $resultsForRedirect[] = [
                    'filename' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'result' => $apiResult
                ];
            }

            // 3. Verifica se há lançamentos para criar antes de chamar o banco
            if (!empty($lancamentosParaCriar)) {
                $user = $request->user();
                $user->lancamentos()->createMany($lancamentosParaCriar);
            }

            DB::commit();

            return redirect()->route('lancamentos.import')->with('success', [
                'success' => true,
                'message' => count($lancamentosParaCriar) . ' lançamentos importados com sucesso!',
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('lancamentos.import')->with('error',[
                'success' => false,
                'message' => 'Ocorreu um erro ao importar os arquivos: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadTemplate()
    {
        // TODO: Gerar arquivo CSV de exemplo com as colunas corretas
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_lancamentos.csv"',
        ];

        $template = 'Data,Descrição,Valor,Categoria,Observações';

        return response($template, 200, $headers);
    }
}
