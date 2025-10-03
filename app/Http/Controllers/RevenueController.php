<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRevenueRequest;
use App\Http\Requests\UpdateRevenueRequest;
use App\Models\Category;
use App\Models\Revenue;
use Illuminate\Http\Request;

class RevenueController extends Controller
{
    public function index(Request $request)
    {
        $query = Revenue::with(['fonte', 'bloco', 'grupo', 'acao']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('observation', 'like', "%{$search}%")
                    ->orWhereHas('fonte', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by date range
        if ($request->filled('date_start')) {
            $query->whereDate('date', '>=', $request->date_start);
        }
        if ($request->filled('date_end')) {
            $query->whereDate('date', '<=', $request->date_end);
        }

        // Filter by category
        if ($request->filled('fonte_id')) {
            $query->where('fonte_id', $request->fonte_id);
        }

        $revenues = $query->orderBy('date', 'desc')->paginate(20);

        // Data for filters
        $fontes = Category::where('type', 'fonte')->where('active', true)->orderBy('name')->get();

        return view('revenues.index', compact('revenues', 'fontes'));
    }

    public function create()
    {
        $fontes = Category::where('type', 'fonte')
            ->with(['children' => function ($query) {
                $query->where('active', true)->orderBy('name');
            }])
            ->where('active', true)
            ->orderBy('name')
            ->get();

        return view('revenues.create', compact('fontes'));
    }

    public function store(StoreRevenueRequest $request)
    {
        $revenue = Revenue::create($request->validated());

        return redirect()->route('revenues.index')
            ->with('success', 'Receita criada com sucesso.');
    }

    public function edit(Revenue $revenue)
    {
        $fontes = Category::where('type', 'fonte')
            ->where('active', true)
            ->orderBy('name')
            ->get();
        $blocos = Category::where('type', 'bloco')
            ->where('parent_id', $revenue->fonte_id)
            ->where('active', true)
            ->orderBy('name')
            ->get();
        $grupos = Category::where('type', 'grupo')
            ->where('parent_id', $revenue->bloco_id)
            ->where('active', true)
            ->orderBy('name')
            ->get();
        $acoes = Category::where('type', 'acao')
            ->where('parent_id', $revenue->grupo_id)
            ->where('active', true)
            ->orderBy('name')
            ->get();

        return view('revenues.edit', compact('revenue', 'fontes', 'blocos', 'grupos', 'acoes'));
    }

    public function update(UpdateRevenueRequest $request, Revenue $revenue)
    {
        $revenue->update($request->validated());

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
            ->where('active', true)
            ->orderBy('name')
            ->get();

        return response()->json($blocos);
    }

    public function getGrupos($blocoId)
    {
        $grupos = Category::where('type', 'grupo')
            ->where('parent_id', $blocoId)
            ->where('active', true)
            ->orderBy('name')
            ->get();

        return response()->json($grupos);
    }

    public function getAcoes($grupoId)
    {
        $acoes = Category::where('type', 'acao')
            ->where('parent_id', $grupoId)
            ->where('active', true)
            ->orderBy('name')
            ->get();

        return response()->json($acoes);
    }
}
