<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Meta;
use App\Repositories\MetaRepository;
use Inertia\Inertia;
use Carbon\Carbon;

class MetaController extends Controller
{   
    protected $metaRepository;

    public function __construct(MetaRepository $metaRepository)
    {
        $this->metaRepository = $metaRepository;
    }

    public function index(Request $request)
    {   
        $metas = Meta::all();

        return Inertia::render('Metas/Index', [
            'metas' => $metas,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo'             => 'required|string|max:255',
            'descricao'          => 'required|string',
            'valor_a_alcancar'   => 'required|numeric|min:0',
            'valor_atual'        => 'nullable|numeric|min:0',
            'data_final'         => 'nullable|date_format:d/m/Y',
            'status'             => 'required|in:pendente,andamento,completa,cancelada',
        ]);

        $validated['valor_atual'] = $validated['valor_atual'] ?? 0;
        $validated['user_id'] = auth()->id();

        if ($validated['data_final']) {
            $validated['data_final'] =  Carbon::createFromFormat('d/m/Y', $validated['data_final'])->format('Y-m-d');
        }

        $meta = Meta::create($validated);

        return redirect()->route('metas.index')->with('success', 'Meta cadastrada com sucesso!');
    }

    public function update(Request $request, $id)
    {   
        try {
            $validated = $request->validate([
                'titulo'             => 'required|string|max:255',
                'descricao'          => 'required|string',
                'valor_a_alcancar'   => 'required|numeric|min:0',
                'valor_atual'        => 'nullable|numeric|min:0',
                'data_final'         => 'nullable|date_format:d/m/Y',
                'status'             => 'required|in:pendente,andamento,completa,cancelada',
            ]);

            if ($validated['data_final']) {
                $validated['data_final'] =  Carbon::createFromFormat('d/m/Y', $validated['data_final'])->format('Y-m-d');
            }

            $this->metaRepository->update($id, $validated);

            return redirect()->route('metas.index')->with('success', 'Meta atualizada com sucesso!');
        } catch (\Illuminate\Validation\ValidationException $e) {

            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            
            return redirect()->route('metas.index')->with('error', 'Não foi possível atualizar a meta. Tente novamente mais tarde.');
        }
        
    
        
    }

    public function destroy($id)
    {
        try {
        
            $this->metaRepository->delete($id);
    
            return redirect()->route('metas.index')->with('success', 'Meta excluída com sucesso!');
        } catch (\Exception $e) {
            // Você pode logar o erro se quiser
            // \Log::error("Erro ao excluir categoria: " . $e->getMessage());
    
            return redirect()->route('metas.index')->with('error', 'Não foi possível excluir a Meta. Tente novamente mais tarde.');
        }
    }

}
