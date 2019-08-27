<?php

namespace Game\Infrastructure\Persistance\Eloquent;

use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{
    public function getIncrementing()
    {
        return false;
    }

    public function getKeyType()
    {
        return 'string';
    }}
