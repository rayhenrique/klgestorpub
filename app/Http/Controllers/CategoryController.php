<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
        $fontes = Category::fontes()->with('children')->get();
        return view('categories.index', compact('fontes'));
    }

    public function create()
    {
        $types = [
            Category::TYPE_FONTE => 'Fonte',
            Category::TYPE_BLOCO => 'Bloco',
            Category::TYPE_GRUPO => 'Grupo',
            Category::TYPE_ACAO => 'Ação'
        ];
        
        $parents = Category::where('type', '!=', Category::TYPE_ACAO)
            ->where('active', true)
            ->get();
            
        return view('categories.create', compact('types', 'parents'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'type' => 'required|in:fonte,bloco,grupo,acao',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validar hierarquia
        if ($request->parent_id) {
            $parent = Category::find($request->parent_id);
            if ($parent->getAllowedChildType() !== $request->type) {
                return redirect()->back()
                    ->withErrors(['type' => 'Tipo inválido para esta hierarquia'])
                    ->withInput();
            }
        } elseif ($request->type !== Category::TYPE_FONTE) {
            return redirect()->back()
                ->withErrors(['type' => 'Apenas Fontes podem não ter pai'])
                ->withInput();
        }

        $category = Category::create($request->all());

        $typeLabel = match($category->type) {
            'fonte' => 'Fonte',
            'bloco' => 'Bloco',
            'grupo' => 'Grupo',
            'acao' => 'Ação',
            default => 'Categoria'
        };

        return redirect()
            ->route('categories.index')
            ->with('success', "{$typeLabel} '{$category->name}' foi criada com sucesso!");
    }

    public function edit(Category $category)
    {
        $types = [
            Category::TYPE_FONTE => 'Fonte',
            Category::TYPE_BLOCO => 'Bloco',
            Category::TYPE_GRUPO => 'Grupo',
            Category::TYPE_ACAO => 'Ação'
        ];
        
        $parents = Category::where('type', '!=', Category::TYPE_ACAO)
            ->where('id', '!=', $category->id)
            ->where('active', true)
            ->get();
            
        return view('categories.edit', compact('category', 'types', 'parents'));
    }

    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'type' => 'required|in:fonte,bloco,grupo,acao',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validar hierarquia
        if ($request->parent_id) {
            $parent = Category::find($request->parent_id);
            if ($parent->getAllowedChildType() !== $request->type) {
                return redirect()->back()
                    ->withErrors(['type' => 'Tipo inválido para esta hierarquia'])
                    ->withInput();
            }
        } elseif ($request->type !== Category::TYPE_FONTE) {
            return redirect()->back()
                ->withErrors(['type' => 'Apenas Fontes podem não ter pai'])
                ->withInput();
        }

        $category->update($request->all());

        $typeLabel = match($category->type) {
            'fonte' => 'Fonte',
            'bloco' => 'Bloco',
            'grupo' => 'Grupo',
            'acao' => 'Ação',
            default => 'Categoria'
        };

        return redirect()
            ->route('categories.index')
            ->with('success', "{$typeLabel} '{$category->name}' foi atualizada com sucesso!");
    }

    public function destroy(Category $category)
    {
        $name = $category->name;
        $typeLabel = match($category->type) {
            'fonte' => 'Fonte',
            'bloco' => 'Bloco',
            'grupo' => 'Grupo',
            'acao' => 'Ação',
            default => 'Categoria'
        };

        $category->delete();

        return redirect()
            ->route('categories.index')
            ->with('success', "{$typeLabel} '{$name}' foi excluída com sucesso!");
    }

    // Métodos para AJAX
    public function getChildren(Category $category)
    {
        $children = $category->children()
            ->where('active', true)
            ->get(['id', 'name', 'type']);

        return response()->json($children);
    }

    public function getAvailableParents(Request $request)
    {
        $type = $request->input('type');
        
        $parentType = match($type) {
            Category::TYPE_BLOCO => Category::TYPE_FONTE,
            Category::TYPE_GRUPO => Category::TYPE_BLOCO,
            Category::TYPE_ACAO => Category::TYPE_GRUPO,
            default => null,
        };

        if (!$parentType) {
            return response()->json([]);
        }

        $parents = Category::where('type', $parentType)
            ->where('active', true)
            ->get();

        return response()->json($parents);
    }
}
