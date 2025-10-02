<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_check_if_user_is_admin()
    {
        // Criar usuário admin
        $adminUser = User::factory()->create(['role' => 'admin']);
        
        // Criar usuário regular
        $regularUser = User::factory()->create(['role' => 'operator']);
        
        // Verificar se o método isAdmin funciona corretamente
        $this->assertTrue($adminUser->isAdmin());
        $this->assertFalse($regularUser->isAdmin());
    }

    /** @test */
    public function it_returns_true_only_for_admin_role()
    {
        // Criar usuário admin
        $adminUser = User::factory()->create(['role' => 'admin']);
        
        // Criar usuário operator
        $operatorUser = User::factory()->create(['role' => 'operator']);
        
        // Verificar se apenas admin retorna true
        $this->assertTrue($adminUser->isAdmin());
        $this->assertFalse($operatorUser->isAdmin());
    }

    /** @test */
    public function it_returns_false_for_operator_role()
    {
        // Criar usuário com role operator
        $user = User::factory()->create(['role' => 'operator']);
        
        // Verificar se retorna false para role operator
        $this->assertFalse($user->isAdmin());
    }

    /** @test */
    public function is_admin_method_does_not_log_unnecessarily()
    {
        // Criar usuário
        $user = User::factory()->create(['role' => 'admin']);
        
        // Capturar logs
        $logMessages = [];
        \Log::listen(function ($message) use (&$logMessages) {
            $logMessages[] = $message;
        });
        
        // Chamar método isAdmin
        $user->isAdmin();
        
        // Verificar que não há logs desnecessários
        $this->assertEmpty($logMessages, 'O método isAdmin() não deve gerar logs desnecessários');
    }
}