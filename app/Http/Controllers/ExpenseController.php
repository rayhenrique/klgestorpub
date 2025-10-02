<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Category;
use App\Models\ExpenseClassification;
use Illuminate\Http\Request;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with(['fonte', 'bloco', 'grupo', 'acao', 'classification']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('observation', 'like', "%{$search}%")
                  ->orWhereHas('fonte', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('classification', function($q) use ($search) {
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
        if ($request->filled('classification_id')) {
            $query->where('expense_classification_id', $request->classification_id);
        }

        $expenses = $query->orderBy('date', 'desc')->paginate(20);
        
        // Data for filters
        $fontes = Category::where('type', 'fonte')->where('active', true)->orderBy('name')->get();
        $classifications = ExpenseClassification::where('active', true)->orderBy('name')->get();

        return view('expenses.index', compact('expenses', 'fontes', 'classifications'));
    }

    public function create()
    {
        $fontes = Category::where('type', 'fonte')
            ->with(['children' => function($query) {
                $query->where('active', true)->orderBy('name');
            }])
            ->where('active', true)
            ->orderBy('name')
            ->get();
        $expenseClassifications = ExpenseClassification::where('active', true)
            ->orderBy('name')
            ->get();
        return view('expenses.create', compact('fontes', 'expenseClassifications'));
    }

    public function store(StoreExpenseRequest $request)
    {
        $expense = Expense::create($request->validated());

        return redirect()->route('expenses.index')
            ->with('success', 'Despesa criada com sucesso.');
    }

    public function edit(Expense $expense)
    {
        $fontes = Category::where('type', 'fonte')
            ->where('active', true)
            ->orderBy('name')
            ->get();
        $blocos = Category::where('type', 'bloco')
            ->where('parent_id', $expense->fonte_id)
            ->where('active', true)
            ->orderBy('name')
            ->get();
        $grupos = Category::where('type', 'grupo')
            ->where('parent_id', $expense->bloco_id)
            ->where('active', true)
            ->orderBy('name')
            ->get();
        $acoes = Category::where('type', 'acao')
            ->where('parent_id', $expense->grupo_id)
            ->where('active', true)
            ->orderBy('name')
            ->get();
        $classifications = ExpenseClassification::where('active', true)
            ->orderBy('name')
            ->get();

        return view('expenses.edit', compact(
            'expense',
            'fontes',
            'blocos',
            'grupos',
            'acoes',
            'classifications'
        ));
    }

    public function update(UpdateExpenseRequest $request, Expense $expense)
    {
        $expense->update($request->validated());

        return redirect()->route('expenses.index')
            ->with('success', 'Despesa atualizada com sucesso.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();

        return redirect()
            ->route('expenses.index')
            ->with('success', 'Despesa excluída com sucesso.');
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
