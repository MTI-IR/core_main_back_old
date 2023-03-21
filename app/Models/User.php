<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    public function companies()
    {
        return $this->hasMany(Company::class);
    }
    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function marks()
    {
        return $this->hasMany(Mark::class);
    }

    public function ticketProjects()
    {
        return $this->belongsToMany(
            Project::class,
            'tickets',
            'user_id',
            'project_id',
        );
    }

    public function markProjects()
    {
        return $this->belongsToMany(
            Project::class,
            'marks',
            'user_id',
            'project_id',
        );
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }
    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }




    public static function boot()
    {
        parent::boot();

        static::deleting(function ($user) { // before delete() method call this
            $user->images()->delete();
            $user->documents()->delete();
            $user->markProjects()->delete();
            $user->ticketProjects()->delete();
            $user->marks()->delete();
            $user->tickets()->delete();
            $user->projects()->delete();
            $user->companies()->delete();
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
    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
