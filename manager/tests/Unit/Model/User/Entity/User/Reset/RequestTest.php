<?php

namespace App\Tests\Unit\Model\User\Entity\User\Reset;

use App\Model\User\Entity\User\ResetToken;
use App\Tests\Builder\User\UserBuilder;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    public function testSuccess(): void
    {
        $now = new \DateTimeImmutable();

        $token = new ResetToken('token', $now->modify('+1 days'));
        $user = (new UserBuilder())->viaEmail()->confirmed()->build();
        $user->requestPasswordReset($token, $now);

        self::assertNotNull($user->getResetToken());
    }

    public function testAlready()
    {
        $now = new \DateTimeImmutable();
        $token = new ResetToken('token', $now->modify('+1 days'));

        $user = (new UserBuilder())->viaEmail()->confirmed()->build();
        $user->requestPasswordReset($token, $now);

        $this->expectExceptionMessage('Resetting is already requested.');
        $user->requestPasswordReset($token, $now);
    }

    public function testExpired()
    {
        $now = new \DateTimeImmutable();
        $user = (new UserBuilder())->viaEmail()->confirmed()->build();

        $token1 = new ResetToken('token', $now->modify('+1 days'));
        $user->requestPasswordReset($token1, $now);

        self::assertEquals($token1, $user->getResetToken());

        $token2 = new ResetToken('token', $now->modify('+3 days'));
        $user->requestPasswordReset($token2, $now->modify('+2 days'));

        self::assertEquals($token2, $user->getResetToken());
    }

    public function testNotConfirmed()
    {
        $now = new \DateTimeImmutable();
        $token = new ResetToken('token', $now->modify('+1 days'));

        $user = (new UserBuilder())->viaEmail()->build();
        $this->expectExceptionMessage('User is not active.');
        $user->requestPasswordReset($token, $now);
    }

    public function testWithoutEmail(): void
    {
        $now = new \DateTimeImmutable();
        $token = new ResetToken('token', $now->modify('+1 days'));

        $user = (new UserBuilder())->viaNetwork()->build();
        $this->expectExceptionMessage('Email is not specified.');

        $user->requestPasswordReset($token, $now);
    }

}