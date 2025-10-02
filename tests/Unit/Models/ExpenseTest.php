<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Expense;
use App\Models\Category;
use App\Models\ExpenseClassification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExpenseTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_validates_category_hierarchy_correctly()
    {
        // Criar hierarquia correta: Fonte > Bloco > Grupo > Ação
        $fonte = Category::factory()->create(['type' => 'fonte', 'parent_id' => null]);
        $bloco = Category::factory()->create(['type' => 'bloco', 'parent_id' => $fonte->id]);
        $grupo = Category::factory()->create(['type' => 'grupo', 'parent_id' => $bloco->id]);
        $acao = Category::factory()->create(['type' => 'acao', 'parent_id' => $grupo->id]);
        $classification = ExpenseClassification::factory()->create();
        
        // Criar despesa com hierarquia correta
        $expense = Expense::factory()->make([
            'fonte_id' => $fonte->id,
            'bloco_id' => $bloco->id,
            'grupo_id' => $grupo->id,
            'acao_id' => $acao->id,
            'expense_classification_id' => $classification->id
        ]);
        
        // Não deve lançar exceção
        $this->assertTrue($expense->save());
    }

    /** @test */
    public function it_throws_exception_for_invalid_bloco_hierarchy()
    {
        // Criar categorias com hierarquia incorreta
        $fonte1 = Category::factory()->create(['type' => 'fonte']);
        $fonte2 = Category::factory()->create(['type' => 'fonte']);
        $bloco = Category::factory()->create(['type' => 'bloco', 'parent_id' => $fonte2->id]);
        $classification = ExpenseClassification::factory()->create();
        
        // Tentar criar despesa com bloco que não pertence à fonte
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('A hierarquia das categorias não está correta. Verifique se as categorias selecionadas seguem a estrutura: Fonte > Bloco > Grupo > Ação.');
        
        Expense::factory()->create([
            'fonte_id' => $fonte1->id,
            'bloco_id' => $bloco->id,
            'expense_classification_id' => $classification->id
        ]);
    }

    /** @test */
    public function it_throws_exception_for_invalid_grupo_hierarchy()
    {
        // Criar hierarquia
        $fonte = Category::factory()->create(['type' => 'fonte']);
        $bloco1 = Category::factory()->create(['type' => 'bloco', 'parent_id' => $fonte->id]);
        $bloco2 = Category::factory()->create(['type' => 'bloco', 'parent_id' => $fonte->id]);
        $grupo = Category::factory()->create(['type' => 'grupo', 'parent_id' => $bloco2->id]);
        $classification = ExpenseClassification::factory()->create();
        
        // Tentar criar despesa com grupo que não pertence ao bloco
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('A hierarquia das categorias não está correta. Verifique se as categorias selecionadas seguem a estrutura: Fonte > Bloco > Grupo > Ação.');
        
        Expense::factory()->create([
            'fonte_id' => $fonte->id,
            'bloco_id' => $bloco1->id,
            'grupo_id' => $grupo->id,
            'expense_classification_id' => $classification->id
        ]);
    }

    /** @test */
    public function it_throws_exception_for_invalid_acao_hierarchy()
    {
        // Criar hierarquia
        $fonte = Category::factory()->create(['type' => 'fonte']);
        $bloco = Category::factory()->create(['type' => 'bloco', 'parent_id' => $fonte->id]);
        $grupo1 = Category::factory()->create(['type' => 'grupo', 'parent_id' => $bloco->id]);
        $grupo2 = Category::factory()->create(['type' => 'grupo', 'parent_id' => $bloco->id]);
        $acao = Category::factory()->create(['type' => 'acao', 'parent_id' => $grupo2->id]);
        $classification = ExpenseClassification::factory()->create();
        
        // Tentar criar despesa com ação que não pertence ao grupo
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('A hierarquia das categorias não está correta. Verifique se as categorias selecionadas seguem a estrutura: Fonte > Bloco > Grupo > Ação.');
        
        Expense::factory()->create([
            'fonte_id' => $fonte->id,
            'bloco_id' => $bloco->id,
            'grupo_id' => $grupo1->id,
            'acao_id' => $acao->id,
            'expense_classification_id' => $classification->id
        ]);
    }

    /** @test */
    public function it_allows_partial_hierarchy()
    {
        // Criar apenas fonte e bloco
        $fonte = Category::factory()->create(['type' => 'fonte']);
        $bloco = Category::factory()->create(['type' => 'bloco', 'parent_id' => $fonte->id]);
        $classification = ExpenseClassification::factory()->create();
        
        // Criar despesa apenas com fonte e bloco (sem grupo e ação)
        $expense = Expense::factory()->make([
            'fonte_id' => $fonte->id,
            'bloco_id' => $bloco->id,
            'grupo_id' => null,
            'acao_id' => null,
            'expense_classification_id' => $classification->id
        ]);
        
        // Deve funcionar normalmente
        $this->assertTrue($expense->save());
    }

    /** @test */
    public function it_validates_hierarchy_on_update()
    {
        // Criar despesa válida
        $fonte = Category::factory()->create(['type' => 'fonte']);
        $bloco = Category::factory()->create(['type' => 'bloco', 'parent_id' => $fonte->id]);
        $classification = ExpenseClassification::factory()->create();
        
        $expense = Expense::factory()->create([
            'fonte_id' => $fonte->id,
            'bloco_id' => $bloco->id,
            'expense_classification_id' => $classification->id
        ]);
        
        // Criar bloco inválido
        $fonte2 = Category::factory()->create(['type' => 'fonte']);
        $blocoInvalido = Category::factory()->create(['type' => 'bloco', 'parent_id' => $fonte2->id]);
        
        // Tentar atualizar com hierarquia inválida
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('A hierarquia das categorias não está correta. Verifique se as categorias selecionadas seguem a estrutura: Fonte > Bloco > Grupo > Ação.');
        
        $expense->update(['bloco_id' => $blocoInvalido->id]);
    }

    /** @test */
    public function it_requires_expense_classification()
    {
        // Criar hierarquia válida
        $fonte = Category::factory()->create(['type' => 'fonte']);
        $bloco = Category::factory()->create(['type' => 'bloco', 'parent_id' => $fonte->id]);
        
        // Tentar criar despesa sem classificação
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Expense::factory()->create([
            'expense_classification_id' => null
        ]);
    }
}