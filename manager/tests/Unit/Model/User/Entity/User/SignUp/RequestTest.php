<?php

namespace App\Tests\Unit\Model\User\Entity\User\SignUp;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\User;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = new User($id = Id::next(), $createdAt = new \DateTimeImmutable());

        self::assertTrue($user->isNew());

        $user->signUpByEmail($email = new Email('test@mail.ru'), $hash = 'hash', $token = 'token');

        self::assertTrue($user->isWait());
        self::assertFalse($user->isActive());

        self::assertEquals($id, $user->getId());
        self::assertEquals($email, $user->getEmail());
        self::assertEquals($hash, $user->getPasswordHash());
        self::assertEquals($token, $user->getConfirmToken());
        self::assertEquals($createdAt, $user->getCreatedAt());

        self::assertTrue($user->getRole()->isUser());
    }

}