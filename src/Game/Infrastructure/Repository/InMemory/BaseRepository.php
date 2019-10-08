<?php

namespace Game\Infrastructure\Repository\InMemory;

abstract class BaseRepository
{
    protected $collection;

    function __construct()
    {
        $this->collection = collect();
    }
}
