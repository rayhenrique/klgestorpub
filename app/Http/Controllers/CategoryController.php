<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::fontes()->with('children');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by active status
        if ($request->filled('active')) {
            $query->where('active', $request->active === '1');
        }

        $fontes = $query->paginate(20);

        return view('categories.index', compact('fontes'));
    }

    public function create()
    {
        $types = [
            Category::TYPE_FONTE => 'Fonte',
            Category::TYPE_BLOCO => 'Bloco',
            Category::TYPE_GRUPO => 'Grupo',
            Category::TYPE_ACAO => 'Ação',
        ];

        $parents = Category::where('type', '!=', Category::TYPE_ACAO)
            ->where('active', true)
            ->get();

        return view('categories.create', compact('types', 'parents'));
    }

    public function store(StoreCategoryRequest $request)
    {
        $data = $request->validated();
        $data['active'] = $request->has('active');

        Category::create($data);

        return redirect()->route('categories.index')
            ->with('success', 'Categoria criada com sucesso.');
    }

    public function edit(Category $category)
    {
        $types = [
            Category::TYPE_FONTE => 'Fonte',
            Category::TYPE_BLOCO => 'Bloco',
            Category::TYPE_GRUPO => 'Grupo',
            Category::TYPE_ACAO => 'Ação',
        ];

        $parents = Category::where('type', '!=', Category::TYPE_ACAO)
            ->where('id', '!=', $category->id)
            ->where('active', true)
            ->get();

        return view('categories.edit', compact('category', 'types', 'parents'));
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $data = $request->validated();
        $data['active'] = $request->has('active');

        $category->update($data);

        return redirect()->route('categories.index')
            ->with('success', 'Categoria atualizada com sucesso.');
    }

    public function destroy(Category $category)
    {
        $name = $category->name;
        $typeLabel = match ($category->type) {
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

        $parentType = match ($type) {
            Category::TYPE_BLOCO => Category::TYPE_FONTE,
            Category::TYPE_GRUPO => Category::TYPE_BLOCO,
            Category::TYPE_ACAO => Category::TYPE_GRUPO,
            default => null,
        };

        if (! $parentType) {
            return response()->json([]);
        }

        $parents = Category::where('type', $parentType)
            ->where('active', true)
            ->get();

        return response()->json($parents);
    }
}
