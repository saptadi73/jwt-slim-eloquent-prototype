<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

trait HasUuid
{
    protected static function bootHasUuid()
    {
        static::creating(function ($model) {
            error_log('bootHasUuid called for: ' . get_class($model));
            if (empty($model->{$model->getKeyName()})) {
                $uuid = (string) Str::uuid();
                error_log('Generated UUID: ' . $uuid);
                $model->{$model->getKeyName()} = $uuid;
            }
        });
    }

    public function getIncrementing()
    {
        return false;
    }

    public function getKeyType()
    {
        return 'string';
    }
}
