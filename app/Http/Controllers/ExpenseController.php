<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Category;
use App\Models\ExpenseClassification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::with(['fonte', 'bloco', 'grupo', 'acao', 'classification'])
            ->orderBy('date', 'desc')
            ->get();
        return view('expenses.index', compact('expenses'));
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
            'expense_classification_id' => 'required|exists:expense_classifications,id',
            'observation' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('expenses.create')
                ->withErrors($validator)
                ->withInput();
        }

        Expense::create($validator->validated());

        return redirect()
            ->route('expenses.index')
            ->with('success', 'Despesa cadastrada com sucesso.');
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

    public function update(Request $request, Expense $expense)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'fonte_id' => 'required|exists:categories,id',
            'bloco_id' => 'required|exists:categories,id',
            'grupo_id' => 'required|exists:categories,id',
            'acao_id' => 'required|exists:categories,id',
            'expense_classification_id' => 'required|exists:expense_classifications,id',
            'observation' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('expenses.edit', $expense)
                ->withErrors($validator)
                ->withInput();
        }

        $expense->update($validator->validated());

        return redirect()
            ->route('expenses.index')
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
