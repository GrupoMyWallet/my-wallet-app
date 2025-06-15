<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Repositories\CategoriaRepository;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoriaController extends Controller
{
    protected $categoriaRepository;

    public function __construct(CategoriaRepository $categoriaRepository)
    {
        $this->categoriaRepository = $categoriaRepository;
    }

    public function index(Request $request)
    {

        $userId = $request->user()->id;

        $categorias = $this->categoriaRepository->getCategoriasComOrçamento($userId);

        return Inertia::render('Categorias/Index', [
            'categorias' => $categorias,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:50'],
            'tipo' => ['required', 'in:despesa,receita'],
        ]);

        Categoria::create([
            'nome' => $validated['nome'],
            'tipo' => $validated['tipo'],
            'user_id' => $request->user()->id,
        ]);

        return redirect()->route('categorias.index')->with('success', 'Categoria cadastrada com sucesso!');
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'nome' => ['required', 'string', 'max:50'],
                'tipo' => ['required', 'in:despesa,receita'],
            ]);

            $this->categoriaRepository->update($id, $validated);

            return redirect()->route('categorias.index')->with('success', 'Categoria atualizada com sucesso!');
        } catch (\Illuminate\Validation\ValidationException $e) {

            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {

            return redirect()->route('categorias.index')->with('error', 'Não foi possível atualizar a categoria. Tente novamente mais tarde.');
        }

    }

    public function destroy($id)
    {
        try {

            $this->categoriaRepository->delete($id);

            return redirect()->route('categorias.index')->with('success', 'Categoria excluída com sucesso!');
        } catch (\Exception $e) {
            // Você pode logar o erro se quiser
            // \Log::error("Erro ao excluir categoria: " . $e->getMessage());

            return redirect()->route('categorias.index')->with('error', 'Não foi possível excluir a Categoria. Tente novamente mais tarde.');
        }
    }
}
