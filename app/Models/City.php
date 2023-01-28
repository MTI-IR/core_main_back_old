<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    public function state()
    {
        $this->belongsTo(State::class);
    }
    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
