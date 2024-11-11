<?php

namespace App\Model\User\Entity;

interface UserRepository
{
    public function hasByEmail(Email $email): bool;
    public function add(User $user): void;
    public function findByConfirmToken(string $token): ?User;
}