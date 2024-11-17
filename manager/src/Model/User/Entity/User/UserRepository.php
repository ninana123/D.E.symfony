<?php

namespace App\Model\User\Entity\User;

interface UserRepository
{
    public function get(Id $id): User;
    public function hasByEmail(Email $email): bool;

    public function add(User $user): void;

    public function findByConfirmToken(string $token): ?User;

    public function hasByNetworkIdentify(string $network, string $identity): bool;

    public function getByEmail(Email $email): User;

    public function findByResetToken(string $token): ?User;
}