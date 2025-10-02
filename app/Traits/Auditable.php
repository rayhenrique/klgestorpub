<?php

namespace App\Traits;

use App\Models\AuditLog;

trait Auditable
{
    protected static function bootAuditable()
    {
        static::updated(function ($model) {
            if ($changes = $model->getDirty()) {
                static::logChanges($model, 'edit', $model->getOriginal(), $changes);
            }
        });

        static::deleted(function ($model) {
            static::logChanges($model, 'delete', $model->getAttributes(), []);
        });
    }

    protected static function logChanges($model, $action, $oldValues, $newValues)
    {
        // Só criar log se houver usuário autenticado
        if (auth()->check()) {
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => $action,
                'model_type' => get_class($model),
                'model_id' => $model->id,
                'old_values' => $oldValues,
                'new_values' => $newValues
            ]);
        }
    }

    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'model');
    }
}