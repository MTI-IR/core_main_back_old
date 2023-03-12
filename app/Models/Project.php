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
        return $this->belongsTo(State::class);
    }
    public function city()
    {
        return $this->belongsTo(City::class);
    }
    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function sub_category()
    {
        return $this->belongsTo(SubCategory::class);
    }
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function tickets()
    {
        return $this->hasMany(ticket::class);
    }
    public function users_ticket()
    {
        return $this->belongsToMany(
            User::class,
            'tickets',
            'project_id',
            'user_id',
        );
    }
    public function marks()
    {
        return $this->hasMany(Mark::class);
    }
    public function users_mark()
    {
        return $this->belongsToMany(
            User::class,
            'tickets',
            'project_id',
            'user_id',
        );
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


    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }
}
