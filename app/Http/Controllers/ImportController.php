<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class ImportController extends Controller
{
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
            ]
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
    public function importStatements(Request $request)
    {
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'file|mimes:pdf,csv|max:20480', // 20MB max
            'document_type' => 'required|in:extrato,fatura',
        ]);

        // TODO: Implementar lógica de importação de extratos/faturas
        // - Processar arquivos PDF (OCR/parsing)
        // - Processar arquivos CSV de bancos
        // - Extrair transações dos documentos
        // - Categorizar automaticamente
        // - Importar dados para o banco
        // - Retornar resultado da importação

        return redirect()->route('lancamentos.import')->with('error', 'Essa funcionalidade ainda será implementada. Tente novamente em breve.');
    }

    public function downloadTemplate()
    {
        // TODO: Gerar arquivo CSV de exemplo com as colunas corretas
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_lancamentos.csv"',
        ];

        $template = "Data,Descrição,Valor,Categoria,Observações";
        

        return response($template, 200, $headers);
    }
}