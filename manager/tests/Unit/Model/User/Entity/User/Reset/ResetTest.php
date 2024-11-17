<?php

namespace App\Tests\Unit\Model\User\Entity\User\Reset;

use App\Model\User\Entity\User\ResetToken;
use App\Tests\Builder\User\UserBuilder;
use PHPUnit\Framework\TestCase;

class ResetTest extends TestCase
{
    public function testSuccess(): void
    {
        $now = new \DateTimeImmutable();

        $token = new ResetToken('token', $now->modify('+1 days'));
        $user = (new UserBuilder())->viaEmail()->confirmed()->build();
        $user->requestPasswordReset($token, $now);

        self::assertNotNull($user->getResetToken());

        $user->passwordReset($now, $hash = 'token');

        self::assertNotNull($user->getResetToken());
        self::assertEquals($hash, $user->getPasswordHash());
    }

    public function testExpiredToken()
    {
        $now = new \DateTimeImmutable();

        $token = new ResetToken('token', $now->modify('+1 modify'));
        $user = (new UserBuilder())->viaEmail()->confirmed()->build();
        $user->requestPasswordReset($token, $now);

        $this->expectExceptionMessage('Reset token is expired.');
        $user->passwordReset($now->modify('+1 days'), 'hash');
    }

    public function testNotRequested()
    {
        $user = (new UserBuilder())->viaEmail()->confirmed()->build();
        $now = new \DateTimeImmutable();

        $this->expectExceptionMessage('Resetting is not requested.');
        $user->passwordReset($now,'hash');
    }

}