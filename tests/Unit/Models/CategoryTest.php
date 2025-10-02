<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Category;
use App\Models\Revenue;
use App\Models\Expense;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_deleted_when_no_dependencies()
    {
        // Criar categoria sem dependências
        $category = Category::factory()->create([
            'type' => 'fonte',
            'active' => true
        ]);
        
        // Verificar se pode ser deletada
        $this->assertTrue($category->canBeDeleted());
        
        // Deletar categoria
        $category->delete();
        
        // Verificar se foi deletada
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    /** @test */
    public function it_cannot_be_deleted_when_has_active_children()
    {
        // Criar categoria pai
        $parent = Category::factory()->create([
            'type' => 'fonte',
            'active' => true
        ]);
        
        // Criar categoria filha ativa
        $child = Category::factory()->create([
            'type' => 'bloco',
            'parent_id' => $parent->id,
            'active' => true
        ]);
        
        // Verificar se não pode ser deletada
        $this->assertFalse($parent->canBeDeleted());
        
        // Tentar deletar deve lançar exceção
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Não é possível excluir esta categoria pois ela possui subcategorias ativas.');
        
        $parent->delete();
    }

    /** @test */
    public function it_cannot_be_deleted_when_has_revenues()
    {
        // Criar categoria
        $category = Category::factory()->create([
            'type' => 'fonte',
            'active' => true
        ]);
        
        // Criar receita associada
        $bloco = Category::factory()->create(['type' => 'bloco', 'parent_id' => $category->id, 'active' => false]);
        Revenue::factory()->create([
            'fonte_id' => $category->id,
            'bloco_id' => $bloco->id,
            'grupo_id' => null,
            'acao_id' => null
        ]);
        
        // Verificar se não pode ser deletada
        $this->assertFalse($category->canBeDeleted());
        
        // Tentar deletar deve lançar exceção
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Não é possível excluir esta categoria pois ela possui receitas associadas.');
        
        $category->delete();
    }

    /** @test */
    public function it_cannot_be_deleted_when_has_expenses()
    {
        // Criar categoria
        $category = Category::factory()->create([
            'type' => 'fonte',
            'active' => true
        ]);
        
        // Criar despesa associada
        $bloco = Category::factory()->create(['type' => 'bloco', 'parent_id' => $category->id, 'active' => false]);
        $classification = \App\Models\ExpenseClassification::factory()->create();
        Expense::factory()->create([
            'fonte_id' => $category->id,
            'bloco_id' => $bloco->id,
            'grupo_id' => null,
            'acao_id' => null,
            'expense_classification_id' => $classification->id
        ]);
        
        // Verificar se não pode ser deletada
        $this->assertFalse($category->canBeDeleted());
        
        // Tentar deletar deve lançar exceção
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Não é possível excluir esta categoria pois ela possui despesas associadas.');
        
        $category->delete();
    }

    /** @test */
    public function it_can_be_deleted_when_has_inactive_children()
    {
        // Criar categoria pai
        $parent = Category::factory()->create([
            'type' => 'fonte',
            'active' => true
        ]);
        
        // Criar categoria filha inativa
        $child = Category::factory()->create([
            'type' => 'bloco',
            'parent_id' => $parent->id,
            'active' => false
        ]);
        
        // Verificar se pode ser deletada (filhas inativas não impedem)
        $this->assertTrue($parent->canBeDeleted());
        
        // Deletar categoria
        $parent->delete();
        
        // Verificar se foi deletada
        $this->assertDatabaseMissing('categories', ['id' => $parent->id]);
    }

    /** @test */
    public function get_deletion_error_message_returns_correct_message()
    {
        // Criar categoria com filha ativa
        $parent = Category::factory()->create(['type' => 'fonte']);
        $child = Category::factory()->create([
            'type' => 'bloco',
            'parent_id' => $parent->id,
            'active' => true
        ]);
        
        $this->assertEquals(
            'Não é possível excluir esta categoria pois ela possui subcategorias ativas.',
            $parent->getDeletionErrorMessage()
        );
        
        // Criar categoria com receita
        $categoryWithRevenue = Category::factory()->create(['type' => 'fonte']);
        $blocoForRevenue = Category::factory()->create(['type' => 'bloco', 'parent_id' => $categoryWithRevenue->id, 'active' => false]);
        Revenue::factory()->create([
            'fonte_id' => $categoryWithRevenue->id,
            'bloco_id' => $blocoForRevenue->id,
            'grupo_id' => null,
            'acao_id' => null
        ]);
        
        $this->assertEquals(
            'Não é possível excluir esta categoria pois ela possui receitas associadas.',
            $categoryWithRevenue->getDeletionErrorMessage()
        );
        
        // Criar categoria com despesa
        $categoryWithExpense = Category::factory()->create(['type' => 'fonte']);
        $blocoForExpense = Category::factory()->create(['type' => 'bloco', 'parent_id' => $categoryWithExpense->id, 'active' => false]);
        $classification = \App\Models\ExpenseClassification::factory()->create();
        Expense::factory()->create([
            'fonte_id' => $categoryWithExpense->id,
            'bloco_id' => $blocoForExpense->id,
            'grupo_id' => null,
            'acao_id' => null,
            'expense_classification_id' => $classification->id
        ]);
        
        $this->assertEquals(
            'Não é possível excluir esta categoria pois ela possui despesas associadas.',
            $categoryWithExpense->getDeletionErrorMessage()
        );
    }
}