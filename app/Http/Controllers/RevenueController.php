<?php

namespace App\Http\Controllers;

use App\Models\Revenue;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RevenueController extends Controller
{
    public function index()
    {
        $revenues = Revenue::with(['fonte', 'bloco', 'grupo', 'acao'])
            ->orderBy('date', 'desc')
            ->get();
        return view('revenues.index', compact('revenues'));
    }

    public function create()
    {
        $fontes = Category::where('type', 'fonte')->orderBy('name')->get();
        return view('revenues.create', compact('fontes'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'fonte_id' => 'required|exists:categories,id',
            'bloco_id' => 'required|exists:categories,id',
            'grupo_id' => 'required|exists:categories,id',
            'acao_id' => 'required|exists:categories,id',
            'observation' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('revenues.create')
                ->withErrors($validator)
                ->withInput();
        }

        Revenue::create($validator->validated());

        return redirect()
            ->route('revenues.index')
            ->with('success', 'Receita cadastrada com sucesso.');
    }

    public function edit(Revenue $revenue)
    {
        $fontes = Category::where('type', 'fonte')->orderBy('name')->get();
        $blocos = Category::where('type', 'bloco')
            ->where('parent_id', $revenue->fonte_id)
            ->orderBy('name')
            ->get();
        $grupos = Category::where('type', 'grupo')
            ->where('parent_id', $revenue->bloco_id)
            ->orderBy('name')
            ->get();
        $acoes = Category::where('type', 'acao')
            ->where('parent_id', $revenue->grupo_id)
            ->orderBy('name')
            ->get();

        return view('revenues.edit', compact('revenue', 'fontes', 'blocos', 'grupos', 'acoes'));
    }

    public function update(Request $request, Revenue $revenue)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'fonte_id' => 'required|exists:categories,id',
            'bloco_id' => 'required|exists:categories,id',
            'grupo_id' => 'required|exists:categories,id',
            'acao_id' => 'required|exists:categories,id',
            'observation' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('revenues.edit', $revenue)
                ->withErrors($validator)
                ->withInput();
        }

        $revenue->update($validator->validated());

        return redirect()
            ->route('revenues.index')
            ->with('success', 'Receita atualizada com sucesso.');
    }

    public function destroy(Revenue $revenue)
    {
        $revenue->delete();

        return redirect()
            ->route('revenues.index')
            ->with('success', 'Receita excluída com sucesso.');
    }

    // Métodos auxiliares para carregar categorias dependentes via AJAX
    public function getBlocos($fonteId)
    {
        $blocos = Category::where('type', 'bloco')
            ->where('parent_id', $fonteId)
            ->orderBy('name')
            ->get();
        return response()->json($blocos);
    }

    public function getGrupos($blocoId)
    {
        $grupos = Category::where('type', 'grupo')
            ->where('parent_id', $blocoId)
            ->orderBy('name')
            ->get();
        return response()->json($grupos);
    }

    public function getAcoes($grupoId)
    {
        $acoes = Category::where('type', 'acao')
            ->where('parent_id', $grupoId)
            ->orderBy('name')
            ->get();
        return response()->json($acoes);
    }
}
