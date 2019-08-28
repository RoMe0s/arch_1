<?php

namespace Game\Infrastructure\Persistance\Eloquent;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'id',
        'name',
        'email',
        'password'
    ];

    protected $appends = [
        'steps',
        'in_game_name'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getIncrementing()
    {
        return false;
    }

    public function getKeyType()
    {
        return 'string';
    }
}
