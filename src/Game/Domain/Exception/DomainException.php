<?php

namespace Game\Domain\Exception;

interface DomainException
{
    public function userMessage(): string;
}
