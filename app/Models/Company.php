<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    public static function bootUuidTrait()
    {
        static::creating(function ($model) {
            $model->keyType = 'string';
            $model->incrementing = false;

            $model->{$model->getKeyName()} = $model->{$model->getKeyName()} ?: (string) Str::orderedUuid();
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

    public function user()
    {
        $this->belongsTo(User::class);
    }
    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
