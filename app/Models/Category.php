<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'name',
        'code',
        'type',
        'parent_id',
        'active',
        'description'
    ];

    // Tipos de categoria
    const TYPE_FONTE = 'fonte';
    const TYPE_BLOCO = 'bloco';
    const TYPE_GRUPO = 'grupo';
    const TYPE_ACAO = 'acao';

    // Relação com o pai
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // Relação com os filhos
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Escopo para cada tipo
    public function scopeFontes($query)
    {
        return $query->where('type', self::TYPE_FONTE);
    }

    public function scopeBlocos($query)
    {
        return $query->where('type', self::TYPE_BLOCO);
    }

    public function scopeGrupos($query)
    {
        return $query->where('type', self::TYPE_GRUPO);
    }

    public function scopeAcoes($query)
    {
        return $query->where('type', self::TYPE_ACAO);
    }

    // Método para obter filhos ativos
    public function getActiveChildren()
    {
        return $this->children()->where('active', true)->get();
    }

    // Método para verificar se pode ter filhos
    public function canHaveChildren(): bool
    {
        return $this->type !== self::TYPE_ACAO;
    }

    // Método para obter o tipo de filho permitido
    public function getAllowedChildType(): ?string
    {
        return match($this->type) {
            self::TYPE_FONTE => self::TYPE_BLOCO,
            self::TYPE_BLOCO => self::TYPE_GRUPO,
            self::TYPE_GRUPO => self::TYPE_ACAO,
            default => null,
        };
    }
}
