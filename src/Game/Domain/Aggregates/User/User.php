<?php

namespace Game\Domain\Aggregates\User;

final class User
{
    private $uuid;

    private $name;

    private function __construct(string $uuid, string $name)
    {
        $this->uuid = $uuid;
        $this->name = $name;
    }

    public static function register(string $uuid, string $name): User
    {
        return new self($uuid, $name);
    }

    public function getId(): string
    {
        return $this->uuid;
    }

    public function getName(): string
    {
        return $this->name;
    }
}