<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Revenue;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RevenueManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;

    private array $categories;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test user
        $this->user = User::factory()->create([
            'role' => 'admin',
        ]);

        // Create test categories hierarchy
        $this->categories = $this->createCategoryHierarchy();
    }

    /**
     * Test that authenticated users can view revenue index
     */
    public function test_authenticated_user_can_view_revenue_index(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('revenues.index'));

        $response->assertOk()
            ->assertViewIs('revenues.index');
    }

    /**
     * Test that unauthenticated users cannot view revenue index
     */
    public function test_unauthenticated_user_cannot_view_revenue_index(): void
    {
        $response = $this->get(route('revenues.index'));

        $response->assertRedirect(route('login'));
    }

    /**
     * Test creating a revenue with valid data
     */
    public function test_can_create_revenue_with_valid_data(): void
    {
        $revenueData = [
            'description' => 'Test Revenue',
            'amount' => 1500.50,
            'date' => Carbon::today()->format('Y-m-d'),
            'fonte_id' => $this->categories['fonte']->id,
            'bloco_id' => $this->categories['bloco']->id,
            'grupo_id' => $this->categories['grupo']->id,
            'acao_id' => $this->categories['acao']->id,
            'observation' => 'Test observation',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('revenues.store'), $revenueData);

        $response->assertRedirect(route('revenues.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('revenues', [
            'description' => 'Test Revenue',
            'amount' => 1500.50,
        ]);
    }

    /**
     * Test validation errors for invalid revenue data
     */
    public function test_revenue_validation_errors(): void
    {
        $invalidData = [
            'description' => '', // Required field empty
            'amount' => -100, // Negative amount
            'date' => Carbon::tomorrow()->format('Y-m-d'), // Future date
            'fonte_id' => 999, // Non-existent category
        ];

        $response = $this->actingAs($this->user)
            ->post(route('revenues.store'), $invalidData);

        $response->assertSessionHasErrors([
            'description',
            'amount',
            'date',
            'fonte_id',
            'bloco_id',
            'grupo_id',
            'acao_id',
        ]);
    }

    /**
     * Test updating a revenue
     */
    public function test_can_update_revenue(): void
    {
        $revenue = Revenue::factory()->create([
            'description' => 'Original Description',
            'amount' => 1000.00,
            'fonte_id' => $this->categories['fonte']->id,
            'bloco_id' => $this->categories['bloco']->id,
            'grupo_id' => $this->categories['grupo']->id,
            'acao_id' => $this->categories['acao']->id,
        ]);

        $updateData = [
            'description' => 'Updated Description',
            'amount' => 1500.00,
        ];

        $response = $this->actingAs($this->user)
            ->put(route('revenues.update', $revenue), $updateData);

        $response->assertRedirect(route('revenues.index'))
            ->assertSessionHas('success');

        $revenue->refresh();
        $this->assertEquals('Updated Description', $revenue->description);
        $this->assertEquals(1500.00, $revenue->amount);
    }

    /**
     * Test deleting a revenue
     */
    public function test_can_delete_revenue(): void
    {
        $revenue = Revenue::factory()->create([
            'fonte_id' => $this->categories['fonte']->id,
            'bloco_id' => $this->categories['bloco']->id,
            'grupo_id' => $this->categories['grupo']->id,
            'acao_id' => $this->categories['acao']->id,
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('revenues.destroy', $revenue));

        $response->assertRedirect(route('revenues.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('revenues', [
            'id' => $revenue->id,
        ]);
    }

    /**
     * Test that revenue amount is properly formatted
     */
    public function test_revenue_amount_formatting(): void
    {
        $revenue = Revenue::factory()->create([
            'amount' => 1234.56,
            'fonte_id' => $this->categories['fonte']->id,
            'bloco_id' => $this->categories['bloco']->id,
            'grupo_id' => $this->categories['grupo']->id,
            'acao_id' => $this->categories['acao']->id,
        ]);

        $this->assertEquals('1234.56', $revenue->amount);
        $this->assertIsNumeric($revenue->amount);
    }

    /**
     * Test revenue category relationships
     */
    public function test_revenue_category_relationships(): void
    {
        $revenue = Revenue::factory()->create([
            'fonte_id' => $this->categories['fonte']->id,
            'bloco_id' => $this->categories['bloco']->id,
            'grupo_id' => $this->categories['grupo']->id,
            'acao_id' => $this->categories['acao']->id,
        ]);

        $this->assertEquals($this->categories['fonte']->name, $revenue->fonte->name);
        $this->assertEquals($this->categories['bloco']->name, $revenue->bloco->name);
        $this->assertEquals($this->categories['grupo']->name, $revenue->grupo->name);
        $this->assertEquals($this->categories['acao']->name, $revenue->acao->name);
    }

    /**
     * Create a complete category hierarchy for testing
     */
    private function createCategoryHierarchy(): array
    {
        $fonte = Category::create([
            'name' => 'Test Fonte',
            'type' => 'fonte',
            'active' => true,
        ]);

        $bloco = Category::create([
            'name' => 'Test Bloco',
            'type' => 'bloco',
            'parent_id' => $fonte->id,
            'active' => true,
        ]);

        $grupo = Category::create([
            'name' => 'Test Grupo',
            'type' => 'grupo',
            'parent_id' => $bloco->id,
            'active' => true,
        ]);

        $acao = Category::create([
            'name' => 'Test Ação',
            'type' => 'acao',
            'parent_id' => $grupo->id,
            'active' => true,
        ]);

        return [
            'fonte' => $fonte,
            'bloco' => $bloco,
            'grupo' => $grupo,
            'acao' => $acao,
        ];
    }
}
