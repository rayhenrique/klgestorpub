<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expense extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'description',
        'amount',
        'date',
        'fonte_id',
        'bloco_id',
        'grupo_id',
        'acao_id',
        'expense_classification_id',
        'observation'
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2'
    ];

    // Relacionamentos com as categorias
    public function fonte(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'fonte_id');
    }

    public function bloco(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'bloco_id');
    }

    public function grupo(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'grupo_id');
    }

    public function acao(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'acao_id');
    }

    // Relacionamento com a classificação de despesa
    public function classification(): BelongsTo
    {
        return $this->belongsTo(ExpenseClassification::class, 'expense_classification_id');
    }
}
