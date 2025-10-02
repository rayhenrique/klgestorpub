<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Revenue;
use App\Models\Expense;
use App\Models\ExpenseClassification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class EagerLoadingTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Criar usuário admin para autenticação
        $this->user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($this->user);
    }

    /** @test */
    public function revenue_controller_uses_eager_loading_in_index()
    {
        // Criar dados de teste
        $fonte = Category::factory()->create(['type' => 'fonte']);
        $bloco = Category::factory()->create(['type' => 'bloco', 'parent_id' => $fonte->id]);
        $grupo = Category::factory()->create(['type' => 'grupo', 'parent_id' => $bloco->id]);
        $acao = Category::factory()->create(['type' => 'acao', 'parent_id' => $grupo->id]);
        
        Revenue::factory()->create([
            'fonte_id' => $fonte->id,
            'bloco_id' => $bloco->id,
            'grupo_id' => $grupo->id,
            'acao_id' => $acao->id
        ]);
        
        // Contar queries executadas
        DB::enableQueryLog();
        
        $response = $this->get(route('revenues.index'));
        
        $queries = DB::getQueryLog();
        DB::disableQueryLog();
        
        // Verificar se a resposta é bem-sucedida
        $response->assertStatus(200);
        
        // Verificar se o eager loading está funcionando (menos queries)
        // Com eager loading, devemos ter menos queries do que sem ele
        $this->assertLessThan(10, count($queries), 'Muitas queries executadas - eager loading pode não estar funcionando');
    }

    /** @test */
    public function expense_controller_uses_eager_loading_in_index()
    {
        // Criar dados de teste
        $fonte = Category::factory()->create(['type' => 'fonte']);
        $bloco = Category::factory()->create(['type' => 'bloco', 'parent_id' => $fonte->id]);
        $grupo = Category::factory()->create(['type' => 'grupo', 'parent_id' => $bloco->id]);
        $acao = Category::factory()->create(['type' => 'acao', 'parent_id' => $grupo->id]);
        $classification = ExpenseClassification::factory()->create();
        
        Expense::factory()->create([
            'fonte_id' => $fonte->id,
            'bloco_id' => $bloco->id,
            'grupo_id' => $grupo->id,
            'acao_id' => $acao->id,
            'expense_classification_id' => $classification->id
        ]);
        
        // Contar queries executadas
        DB::enableQueryLog();
        
        $response = $this->get(route('expenses.index'));
        
        $queries = DB::getQueryLog();
        DB::disableQueryLog();
        
        // Verificar se a resposta é bem-sucedida
        $response->assertStatus(200);
        
        // Verificar se o eager loading está funcionando
        $this->assertLessThan(10, count($queries), 'Muitas queries executadas - eager loading pode não estar funcionando');
    }

    /** @test */
    public function category_controller_uses_eager_loading_in_index()
    {
        // Criar hierarquia de categorias
        $fonte = Category::factory()->create(['type' => 'fonte']);
        $bloco = Category::factory()->create(['type' => 'bloco', 'parent_id' => $fonte->id]);
        $grupo = Category::factory()->create(['type' => 'grupo', 'parent_id' => $bloco->id]);
        
        // Contar queries executadas
        DB::enableQueryLog();
        
        $response = $this->get(route('categories.index'));
        
        $queries = DB::getQueryLog();
        DB::disableQueryLog();
        
        // Verificar se a resposta é bem-sucedida
        $response->assertStatus(200);
        
        // Verificar se o eager loading está funcionando
        $this->assertLessThan(8, count($queries), 'Muitas queries executadas - eager loading pode não estar funcionando');
    }

    /** @test */
    public function expense_classification_controller_uses_eager_loading()
    {
        // Criar classificações com despesas
        $classification = ExpenseClassification::factory()->create();
        $fonte = Category::factory()->create(['type' => 'fonte']);
        
        Expense::factory()->create([
            'expense_classification_id' => $classification->id,
            'fonte_id' => $fonte->id
        ]);
        
        // Contar queries executadas
        DB::enableQueryLog();
        
        $response = $this->get(route('expense-classifications.index'));
        
        $queries = DB::getQueryLog();
        DB::disableQueryLog();
        
        // Verificar se a resposta é bem-sucedida
        $response->assertStatus(200);
        
        // Verificar se o eager loading está funcionando
        $this->assertLessThan(5, count($queries), 'Muitas queries executadas - eager loading pode não estar funcionando');
    }

    /** @test */
    public function revenue_create_page_loads_categories_efficiently()
    {
        // Criar hierarquia de categorias
        $fonte = Category::factory()->create(['type' => 'fonte', 'active' => true]);
        Category::factory()->create(['type' => 'bloco', 'parent_id' => $fonte->id, 'active' => true]);
        
        // Contar queries executadas
        DB::enableQueryLog();
        
        $response = $this->get(route('revenues.create'));
        
        $queries = DB::getQueryLog();
        DB::disableQueryLog();
        
        // Verificar se a resposta é bem-sucedida
        $response->assertStatus(200);
        
        // Verificar se as categorias são carregadas eficientemente
        $this->assertLessThan(6, count($queries), 'Muitas queries para carregar página de criação');
    }

    /** @test */
    public function expense_create_page_loads_data_efficiently()
    {
        // Criar dados necessários
        $fonte = Category::factory()->create(['type' => 'fonte', 'active' => true]);
        Category::factory()->create(['type' => 'bloco', 'parent_id' => $fonte->id, 'active' => true]);
        ExpenseClassification::factory()->create(['active' => true]);
        
        // Contar queries executadas
        DB::enableQueryLog();
        
        $response = $this->get(route('expenses.create'));
        
        $queries = DB::getQueryLog();
        DB::disableQueryLog();
        
        // Verificar se a resposta é bem-sucedida
        $response->assertStatus(200);
        
        // Verificar se os dados são carregados eficientemente
        $this->assertLessThan(8, count($queries), 'Muitas queries para carregar página de criação de despesas');
    }

    /** @test */
    public function ajax_endpoints_filter_by_active_status()
    {
        // Criar categorias ativas e inativas
        $fonte = Category::factory()->create(['type' => 'fonte', 'active' => true]);
        $blocoAtivo = Category::factory()->create([
            'type' => 'bloco', 
            'parent_id' => $fonte->id, 
            'active' => true
        ]);
        $blocoInativo = Category::factory()->create([
            'type' => 'bloco', 
            'parent_id' => $fonte->id, 
            'active' => false
        ]);
        
        // Testar endpoint de blocos para receitas
        $response = $this->get(route('revenues.getBlocos', ['fonte_id' => $fonte->id]));
        
        $response->assertStatus(200);
        $data = $response->json();
        
        // Verificar se apenas categorias ativas são retornadas
        $this->assertCount(1, $data);
        $this->assertEquals($blocoAtivo->id, $data[0]['id']);
    }
}