<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Reset\Reset;

class Command
{
    public string $token;
    public string $password;

    public function __construct(string $token)
    {
        $this->token = $token;
    }
}