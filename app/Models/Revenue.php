<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Revenue extends Model
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
        'observation'
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2'
    ];

    // Relacionamentos com as categorias
    public function fonte()
    {
        return $this->belongsTo(Category::class, 'fonte_id');
    }

    public function bloco()
    {
        return $this->belongsTo(Category::class, 'bloco_id');
    }

    public function grupo()
    {
        return $this->belongsTo(Category::class, 'grupo_id');
    }

    public function acao()
    {
        return $this->belongsTo(Category::class, 'acao_id');
    }
}
