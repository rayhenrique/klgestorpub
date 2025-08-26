<?php

namespace App\Http\Controllers;

use App\Models\ExpenseClassification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExpenseClassificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $classifications = ExpenseClassification::orderBy('name')->get();
        return view('expense-classifications.index', compact('classifications'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('expense-classifications.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:expense_classifications',
            'description' => 'nullable|string',
            'active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('expense-classifications.create')
                ->withErrors($validator)
                ->withInput();
        }

        ExpenseClassification::create($validator->validated());

        return redirect()
            ->route('expense-classifications.index')
            ->with('success', 'Classificação de despesa criada com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ExpenseClassification $expenseClassification)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ExpenseClassification $expenseClassification)
    {
        return view('expense-classifications.edit', compact('expenseClassification'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ExpenseClassification $expenseClassification)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:expense_classifications,code,' . $expenseClassification->id,
            'description' => 'nullable|string',
            'active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('expense-classifications.edit', $expenseClassification)
                ->withErrors($validator)
                ->withInput();
        }

        $expenseClassification->update($validator->validated());

        return redirect()
            ->route('expense-classifications.index')
            ->with('success', 'Classificação de despesa atualizada com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ExpenseClassification $expenseClassification)
    {
        $expenseClassification->delete();

        return redirect()
            ->route('expense-classifications.index')
            ->with('success', 'Classificação de despesa excluída com sucesso.');
    }
}
