<?php

namespace Game\Infrastructure\Persistance\Eloquent;

class Step extends BaseModel
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'id',
        'game_id',
        'user_id',
        'coordinate_x',
        'coordinate_y'
    ];
}
