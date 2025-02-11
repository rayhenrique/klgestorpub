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
            ->with('success', sprintf('A %s "%s" foi criada com sucesso!', $typeLabel, $category->name));
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
            ->with('success', sprintf('A %s "%s" foi atualizada com sucesso!', $typeLabel, $category->name));
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

        // Verificar dependências
        $hasDependencies = false;
        $dependencies = [];

        // Verificar despesas
        if ($category->expenses()->exists()) {
            $hasDependencies = true;
            $dependencies[] = 'despesas';
        }

        // Verificar receitas
        if ($category->revenues()->exists()) {
            $hasDependencies = true;
            $dependencies[] = 'receitas';
        }

        // Verificar categorias filhas
        if ($category->children()->exists()) {
            $hasDependencies = true;
            $dependencies[] = 'subcategorias';
        }

        if ($hasDependencies) {
            // Inativar a categoria ao invés de excluir
            $category->update(['active' => false]);
            
            $dependenciesStr = implode(', ', $dependencies);
            return redirect()
                ->route('categories.index')
                ->with('warning', sprintf('A %s "%s" não pode ser excluída pois possui %s associadas. A categoria foi inativada para preservar o histórico.',
                    $typeLabel,
                    $name,
                    $dependenciesStr
                ));
        }

        try {
            $category->delete();
            return redirect()
                ->route('categories.index')
                ->with('success', sprintf('A %s "%s" foi excluída com sucesso!', $typeLabel, $name));
        } catch (\Exception $e) {
            // Tentar inativar em caso de erro na exclusão
            try {
                $category->update(['active' => false]);
                return redirect()
                    ->route('categories.index')
                    ->with('warning', sprintf('Não foi possível excluir a %s "%s" devido a dependências. A categoria foi inativada para preservar o histórico.',
                        $typeLabel,
                        $name
                    ));
            } catch (\Exception $e2) {
                return redirect()
                    ->route('categories.index')
                    ->with('error', sprintf('Erro ao processar a %s "%s". Por favor, tente novamente.',
                        $typeLabel,
                        $name
                    ));
            }
        }
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
