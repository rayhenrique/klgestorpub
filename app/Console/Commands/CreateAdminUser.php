<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Criar usuário administrador com credenciais específicas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            // Verificar se o usuário já existe
            $existingUser = User::where('email', 'rayhenrique@gmail.com')->first();

            if ($existingUser) {
                $this->info('Usuário já existe. Atualizando dados...');
                $existingUser->update([
                    'name' => 'Ray Henrique',
                    'password' => Hash::make('1508rcrc'),
                    'role' => 'admin',
                    'active' => true,
                ]);
                $this->info('Usuário atualizado com sucesso!');
            } else {
                // Criar novo usuário
                $user = User::create([
                    'name' => 'Ray Henrique',
                    'email' => 'rayhenrique@gmail.com',
                    'password' => Hash::make('1508rcrc'),
                    'role' => 'admin',
                    'active' => true,
                ]);
                $this->info('Usuário administrador criado com sucesso!');
            }

            // Verificar se foi criado
            $user = User::where('email', 'rayhenrique@gmail.com')->first();
            if ($user) {
                $this->info('Verificação: Usuário encontrado no banco de dados');
                $this->line('ID: '.$user->id);
                $this->line('Nome: '.$user->name);
                $this->line('Email: '.$user->email);
                $this->line('Role: '.$user->role);
                $this->line('Ativo: '.($user->active ? 'Sim' : 'Não'));
            } else {
                $this->error('ERRO: Usuário não foi encontrado após criação!');

                return 1;
            }

            return 0;

        } catch (\Exception $e) {
            $this->error('ERRO: '.$e->getMessage());

            return 1;
        }
    }
}
