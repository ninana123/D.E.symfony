<?php

namespace App\Tests\Unit\Model\User\Entity\User\Network;

use App\Model\User\Entity\Id;
use App\Model\User\Entity\Network;
use App\Model\User\Entity\User;
use PHPUnit\Framework\TestCase;

class AuthTest extends TestCase
{
    public function testSuccess()
    {
        $user = new User(Id::next(), new \DateTimeImmutable());

        $user->signUpByNetwork($network = 'vk', $identify = '000001');

        self::assertTrue($user->isActive());

        self::assertCount(1, $networks = $user->getNetworks());
        self::assertInstanceOf(Network::class, $first = reset($networks));
        self::assertEquals($network, $first->getNetWork());
        self::assertEquals($identify, $first->getIdentify());
    }

    public function testAlready()
    {
        $user = new User(Id::next(), new \DateTimeImmutable());

        $user->signUpByNetwork('vk', '000001');

        $this->expectExceptionMessage('User is already signed up');

        $user->signUpByNetwork('vk', '000001');

    }
}