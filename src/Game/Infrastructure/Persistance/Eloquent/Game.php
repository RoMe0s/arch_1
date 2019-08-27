<?php

namespace Game\Infrastructure\Persistance\Eloquent;

class Game extends BaseModel
{
    public $timestamps = false;

    protected $fillable = [
        'id',
        'owner_id',
        'owner_name',
        'competitor_id',
        'competitor_name',
        'winner_id',
        'started_at',
        'ended_at'
    ];

    protected $dates = [
        'started_at',
        'ended_at'
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function competitor()
    {
        return $this->belongsTo(User::class, 'competitor_id');
    }

    public function steps()
    {
        return $this->hasMany(Step::class);
    }
}
