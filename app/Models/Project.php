<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory;


    public function state()
    {
        $this->belongsTo(State::class);
    }
    public function city()
    {
        $this->belongsTo(City::class);
    }
    public function tag()
    {
        $this->belongsTo(Tag::class);
    }
    public function category()
    {
        $this->belongsTo(Category::class);
    }
    public function sub_category()
    {
        $this->belongsTo(SubCategory::class);
    }
    public function company()
    {
        $this->belongsTo(Company::class);
    }

    public function user()
    {
        $this->belongsTo(User::class);
    }
    public function permission()
    {
        $this->belongsTo(Permission::class);
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
