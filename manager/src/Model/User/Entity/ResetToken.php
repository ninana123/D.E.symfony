<?php

namespace App\Model\User\Entity;

use Webmozart\Assert\Assert;

class ResetToken
{
    private string $token;
    private \DateTimeImmutable $expires;

    public function __construct(string $token, \DateTimeImmutable $expires)
    {
        Assert::notEmpty($token);

        $this->token = $token;
        $this->expires = $expires;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function isExpiredTo(\DateTimeImmutable $date): bool
    {
        return $this->expires <= $date;
    }

}