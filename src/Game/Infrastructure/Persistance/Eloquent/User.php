<?php

namespace Game\Infrastructure\Persistance\Eloquent;

use Illuminate\Support\Collection;

class User extends BaseModel
{
    public $timestamps = false;

    protected $fillable = [
        'id'
    ];

    protected $appends = [
        'steps',
        'name'
    ];

    public function getStepsAttribute(): Collection
    {
        return $this->steps ?? collect();
    }

    public function getNameAttribute()
    {
        return $this->name;
    }
}
