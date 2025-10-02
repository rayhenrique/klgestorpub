<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Índices otimizados para a tabela categories
        Schema::table('categories', function (Blueprint $table) {
            $table->index(['type', 'active'], 'idx_categories_type_active');
            $table->index(['parent_id', 'active'], 'idx_categories_parent_active');
            $table->index(['code', 'type'], 'idx_categories_code_type');
        });

        // Índices otimizados para a tabela revenues
        Schema::table('revenues', function (Blueprint $table) {
            $table->index(['acao_id', 'date'], 'idx_revenues_acao_date');
            $table->index(['date', 'amount'], 'idx_revenues_date_amount');
            $table->index(['created_at', 'acao_id'], 'idx_revenues_created_acao');
        });

        // Índices otimizados para a tabela expenses
        Schema::table('expenses', function (Blueprint $table) {
            $table->index(['acao_id', 'date'], 'idx_expenses_acao_date');
            $table->index(['date', 'amount'], 'idx_expenses_date_amount');
            $table->index(['created_at', 'acao_id'], 'idx_expenses_created_acao');
            $table->index(['expense_classification_id', 'date'], 'idx_expenses_classification_date');
        });

        // Índices otimizados para a tabela expense_classifications
        Schema::table('expense_classifications', function (Blueprint $table) {
            $table->index(['code', 'active'], 'idx_expense_classifications_code_active');
            $table->index(['name', 'active'], 'idx_expense_classifications_name_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remover índices da tabela categories
        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex('idx_categories_type_active');
            $table->dropIndex('idx_categories_parent_active');
            $table->dropIndex('idx_categories_code_type');
        });

        // Remover índices da tabela revenues
        Schema::table('revenues', function (Blueprint $table) {
            $table->dropIndex('idx_revenues_acao_date');
            $table->dropIndex('idx_revenues_date_amount');
            $table->dropIndex('idx_revenues_created_acao');
        });

        // Remover índices da tabela expenses
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropIndex('idx_expenses_acao_date');
            $table->dropIndex('idx_expenses_date_amount');
            $table->dropIndex('idx_expenses_created_acao');
            $table->dropIndex('idx_expenses_classification_date');
        });

        // Remover índices da tabela expense_classifications
        Schema::table('expense_classifications', function (Blueprint $table) {
            $table->dropIndex('idx_expense_classifications_code_active');
            $table->dropIndex('idx_expense_classifications_name_active');
        });
    }
};
