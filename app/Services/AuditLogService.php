<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Logs model changes (create/update/delete) to the audit_logs table.
 */
class AuditLogService
{
    /**
     * Log a model creation.
     */
    public function logCreated(Model $model): AuditLog
    {
        return AuditLog::create([
            'user_id' => Auth::id(),
            'model_type' => get_class($model),
            'model_id' => $model->getKey(),
            'action' => 'created',
            'old_values' => null,
            'new_values' => $model->getAttributes(),
        ]);
    }

    /**
     * Log a model update.
     */
    public function logUpdated(Model $model): AuditLog
    {
        return AuditLog::create([
            'user_id' => Auth::id(),
            'model_type' => get_class($model),
            'model_id' => $model->getKey(),
            'action' => 'updated',
            'old_values' => $model->getOriginal(),
            'new_values' => $model->getChanges(),
        ]);
    }

    /**
     * Log a model deletion.
     */
    public function logDeleted(Model $model): AuditLog
    {
        return AuditLog::create([
            'user_id' => Auth::id(),
            'model_type' => get_class($model),
            'model_id' => $model->getKey(),
            'action' => 'deleted',
            'old_values' => $model->getAttributes(),
            'new_values' => null,
        ]);
    }
}
