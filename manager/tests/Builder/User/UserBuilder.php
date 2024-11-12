<?php

namespace App\Tests\Builder\User;

use App\Model\User\Entity\Email;
use App\Model\User\Entity\Id;
use App\Model\User\Entity\User;

class UserBuilder
{
    private Id $id;
    private \DateTimeImmutable $createdAt;

    private ?Email $email = null;
    private string $hash;
    private string $token;
    private bool $confirmed = false;

    private ?string $network = null;
    private ?string $identify = null;

    public function __construct()
    {
        $this->id = Id::next();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function viaEmail(Email $email = null, string $hash = null, string $token = null): self
    {
        $clone = $this;

        $clone->email = $email ?? new Email('test@mail.ru');
        $clone->hash = $hash ?? 'hash';
        $clone->token = $token ?? 'token';

        return $clone;
    }

    public function confirmed(): self
    {
        $clone = $this;
        $clone->confirmed = true;

        return $clone;
    }

    public function viaNetwork(string $network = null, string $identify = null): self
    {
        $clone = $this;

        $clone->network = $network ?? 'vk';
        $clone->identify = $identify ?? '000001';

        return $clone;
    }

    public function build(): User
    {
        $user = new User($this->id, $this->createdAt);

        if ($this->email) {
            $user->signUpByEmail(
                $this->email,
                $this->hash,
                $this->token
            );

            if ($this->confirmed) {
                $user->confirmSignUp();
            }
        }

        if ($this->network) {
            $user->signUpByNetwork($this->network, $this->identify);
        }

        return $user;
    }
}