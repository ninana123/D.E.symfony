<?php

namespace App\Model\User\Entity\User;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embedded;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="user_users",uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"email"}),
 *     @ORM\UniqueConstraint(columns={"reset_token_token"})
 * })
 */
class User
{
    private const STATUS_NEW = 'new';
    private const STATUS_WAIT = 'wait';
    private const STATUS_ACTIVE = 'active';

    /**
     * @ORM\Column(type="user_user_id")
     * @ORM\Id()
     */
    private Id $id;

    /**
     * @ORM\Column(type="user_user_email",nullable=true)
     */
    private ?Email $email = null;

    /**
     * @ORM\Column(type="string",nullable=true)
     */
    private ?string $passwordHash = null;

    /**
     * @ORM\Column(type="string",nullable=true)
     */
    private ?string $confirmToken = null;
    /**
     * @ORM\Column(type="string",length=16)
     */
    private string $status;

    /**
     * @ORM\Column(type="user_user_role")
     */
    private Role $role;

    /**
     * @Embedded(class="ResetToken",columnPrefix="reset_token_")
     */
    private ?ResetToken $resetToken = null;

    /**
     * @ORM\OneToMany(targetEntity="Network",mappedBy="user",orphanRemoval=true,cascade={"persist"})
     */
    private ArrayCollection $networks;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private \DateTimeImmutable $createdAt;

    public function __construct(Id $id, \DateTimeImmutable $createdAt)
    {
        $this->id = $id;
        $this->status = self::STATUS_NEW;
        $this->createdAt = $createdAt;
        $this->role = Role::user();
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

        if ($this->resetToken->isExpiredTo($date)) {
            throw new \DomainException('Reset token is expired.');
        }

        $this->passwordHash = $passwordHash;
    }

    public function confirmSignUp(): void
    {
        if (!$this->isWait()) {
            throw new \DomainException('User is already confirmed.');
        }

        $this->status = self::STATUS_ACTIVE;
        $this->confirmToken = null;
    }

    public function changeRole(Role $role): void
    {
        if ($this->role->isEqual($role)) {
            throw new \DomainException('Role is already same.');
        }

        $this->role = $role;
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

    // array, а не ArrayCollection, иначе может добавить черзе метод add
    public function getNetworks(): array
    {
        return $this->networks->toArray();
    }

    public function getResetToken(): ?ResetToken
    {

        return $this->resetToken;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function setRole(Role $role): void
    {
        $this->role = $role;
    }

    /**
     * @ORM\PostLoad()
     */
    public function checkEmbeds(): void
    {
        if ($this->resetToken->isEmpty()) {
            $this->resetToken = null;
        }
    }
}