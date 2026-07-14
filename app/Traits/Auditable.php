<?php

namespace App\Traits;

use App\Models\AuditTrail;
use Illuminate\Support\Facades\Request;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function ($model) {
            $model->audit('create', null, $model->getAttributes());
        });

        static::updated(function ($model) {
            $model->audit('update', $model->getOriginal(), $model->getChanges());
        });

        static::deleted(function ($model) {
            $model->audit('delete', $model->getAttributes(), null);
        });
    }

    protected function audit($action, $oldValues, $newValues)
    {
        // Hindari merekam jika berjalan di console/cli
        if (app()->runningInConsole()) {
            return;
        }

        AuditTrail::create([
            'user_id'    => auth()->id(),
            'action'     => $action,
            'model_type' => get_class($this),
            'model_id'   => $this->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
