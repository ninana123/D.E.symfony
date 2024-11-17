<?php

namespace App\Tests\Unit\Model\User\Entity\User\Network;

use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Network;
use App\Model\User\Entity\User\User;
use PHPUnit\Framework\TestCase;

class AuthTest extends TestCase
{
    public function testSuccess()
    {
        $user = new User(Id::next(), new \DateTimeImmutable());
        $user->signUpByNetwork($network = 'vk', $identity = '000001');

        self::assertTrue($user->isActive());

        self::assertCount(1, $networks = $user->getNetworks());
        self::assertInstanceOf(Network::class, $first = reset($networks));
        self::assertEquals($network, $first->getNetWork());
        self::assertEquals($identity, $first->getIdentity());

        self::assertTrue($user->getRole()->isUser());
    }

    public function testAlready()
    {
        $user = new User(Id::next(), new \DateTimeImmutable());

        $user->signUpByNetwork('vk', '000001');

        $this->expectExceptionMessage('User is already signed up');

        $user->signUpByNetwork('vk', '000001');

    }
}