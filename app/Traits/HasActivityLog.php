<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait HasActivityLog
{
    public static function bootHasActivityLog(): void
    {
        static::created(function (Model $model): void {
            $model->writeActivityLog('created', null, $model->getAttributes());
        });

        static::updated(function (Model $model): void {
            $changes = $model->getChanges();
            unset($changes['updated_at']);

            if (empty($changes)) {
                return;
            }

            $oldValues = [];

            foreach (array_keys($changes) as $key) {
                $oldValues[$key] = $model->getOriginal($key);
            }

            $model->writeActivityLog('updated', $oldValues, $changes);
        });

        static::deleted(function (Model $model): void {
            $model->writeActivityLog('deleted', $model->getOriginal(), null);
        });
    }

    protected function writeActivityLog(string $action, ?array $oldValues, ?array $newValues): void
    {
        if ($this instanceof ActivityLog) {
            return;
        }

        ActivityLog::query()->create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => static::class,
            'model_id' => $this->getKey(),
            'description' => sprintf('%s %s', class_basename(static::class), $action),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ]);
    }
}