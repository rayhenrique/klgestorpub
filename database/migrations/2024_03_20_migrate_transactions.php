<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Migrar receitas
        DB::statement("
            INSERT INTO transactions (
                date,
                type,
                amount,
                description,
                category_id,
                expense_classification_id,
                created_at,
                updated_at
            )
            SELECT
                date,
                'revenue' as type,
                amount,
                description,
                acao_id as category_id,
                NULL as expense_classification_id,
                created_at,
                updated_at
            FROM revenues
        ");

        // Migrar despesas
        DB::statement("
            INSERT INTO transactions (
                date,
                type,
                amount,
                description,
                category_id,
                expense_classification_id,
                created_at,
                updated_at
            )
            SELECT
                date,
                'expense' as type,
                amount,
                description,
                acao_id as category_id,
                expense_classification_id,
                created_at,
                updated_at
            FROM expenses
        ");
    }

    public function down()
    {
        DB::table('transactions')->truncate();
    }
}; 