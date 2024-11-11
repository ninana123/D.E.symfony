<?php

namespace App\Model\User\Entity;


use Ramsey\Uuid\Uuid;

class Network
{
    private string $id;
    private User $user;
    private string $network;
    private string $identify;

    public function __construct(User $user, string $network, string $identify)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->user = $user;
        $this->network = $network;
        $this->identify = $identify;
    }

    public function isForNetwork(string $network): bool
    {
        return $this->network === $network;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getIdentify(): string
    {
        return $this->identify;
    }

    public function getNetwork(): string
    {
        return $this->network;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}