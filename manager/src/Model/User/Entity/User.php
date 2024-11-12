<?php

namespace App\Model\User\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Mailer\Header\TagHeader;

class User
{
    private const STATUS_NEW = 'new';
    private const STATUS_WAIT = 'wait';
    private const STATUS_ACTIVE = 'active';

    private Id $id;
    private ?Email $email = null;
    private string $passwordHash;
    private ?string $confirmToken = null;
    private string $status;
    private ?ResetToken $resetToken = null;
    private ArrayCollection $networks;
    private \DateTimeImmutable $createdAt;

    public function __construct(Id $id, \DateTimeImmutable $createdAt)
    {
        $this->id = $id;
        $this->status = self::STATUS_NEW;
        $this->createdAt = $createdAt;
        $this->networks = new ArrayCollection();
    }

    public function signUpByEmail(Email $email, string $passwordHash, string $confirmToken): void
    {
        if (!$this->isNew()) {
            throw new \DomainException('User is already signed up');
        }

        $this->email = $email;
        $this->passwordHash = $passwordHash;
        $this->confirmToken = $confirmToken;
        $this->status = self::STATUS_WAIT;
    }

    public function signUpByNetwork(string $network, string $identify): void
    {
        if (!$this->isNew()) {
            throw new \DomainException('User is already signed up');
        }

        $this->attachNetwork($network, $identify);
        $this->status = self::STATUS_ACTIVE;
    }

    private function attachNetwork(string $network, string $identify): void
    {
        foreach ($this->networks as $existing) {
            if ($existing->isForNetwork($network)) {
                throw new \DomainException('Network is already attached');
            }
        }

        $this->networks->add(new Network($this, $network, $identify));
    }

    public function requestPasswordReset(ResetToken $token, \DateTimeImmutable $date): void
    {
        if (!$this->isActive()) {
            throw new \DomainException('User is not active.');
        }
        if (!$this->email) {
            throw new \DomainException('Email is not specified.');
        }

        if ($this->resetToken && !$this->resetToken->isExpiredTo($date)) {
            throw new \DomainException('Resetting is already requested.');
        }
        $this->resetToken = $token;
    }

    public function passwordReset(\DateTimeImmutable $date, string $passwordHash)
    {
        if (!$this->resetToken) {
            throw new \DomainException('Resetting is not requested.');
        }

        if ($this->resetToken->isExpiredTo($date)){
            throw new \DomainException('Reset token is expired.');
        }

        $this->passwordHash = $passwordHash;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getConfirmToken(): ?string
    {
        return $this->confirmToken;
    }

    public function isNew(): bool
    {
        return $this->status === self::STATUS_NEW;
    }

    public function isWait(): bool
    {
        return $this->status === self::STATUS_WAIT;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function confirmSignUp(): void
    {
        if (!$this->isWait()) {
            throw new \DomainException('User is already confirmed.');
        }

        $this->status = self::STATUS_ACTIVE;
        $this->confirmToken = null;
    }

    // array, а не ArrayCollection, иначе может добавить черзе метод add
    public function getNetworks(): array
    {
        return $this->networks->toArray();
    }

    public function getResetToken(): ?ResetToken
    {
        return $this->resetToken;
    }
}