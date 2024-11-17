<?php

namespace App\Model\User\Service;

use App\Model\User\Entity\User\ResetToken;
use Ramsey\Uuid\Uuid;

class ResetTokenizer
{
    private \DateInterval $dateInterval;

    public function __construct(\DateInterval $dateInterval)
    {
        $this->dateInterval = $dateInterval;
    }

    public function generate(): ResetToken
    {
        return new ResetToken(Uuid::uuid4()->toString(), (new \DateTimeImmutable())->add($this->dateInterval));
    }
}