<?php

namespace App\Http\Controllers;

use App\Services\PythonApiService;
use Illuminate\Http\Request;
use Inertia\Inertia;

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

    /**
     * Processa a importação de extratos/faturas (PDF/CSV)
     */
    public function importArquivo(Request $request)
    {
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'file|mimes:pdf,csv|max:20480',
            'document_type' => 'required|in:extrato,fatura'
        ]);

        try {
            $results = [];
            
            // Processa cada arquivo
            foreach ($request->file('files') as $file) {
                $result = $this->pythonApi->processarArquivo(
                    $file,
                    ['document_type' => $request->input('document_type')]
                );
                
                $results[] = [
                    'filename' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'result' => $result
                ];
            }

            return redirect()->route('lancamentos.import')->with('success', [
                'success' => true,
                'message' => 'Arquivos processados com sucesso',
                'total_files' => count($results),
                'document_type' => $request->input('document_type'),
                'data' => $results
            ]);
            
        } catch (\Exception $e) {
            return redirect()->route('lancamentos.import')->with('error',[
                'success' => false,
                'message' => $e->getMessage()
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
