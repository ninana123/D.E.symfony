<?php

namespace App\Model\User\UseCase\Reset\Reset;


use App\Model\Flusher;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Service\PasswordHasher;

class Handler
{

    private UserRepository $users;
    private PasswordHasher $hasher;
    private Flusher $flusher;

    public function __construct(UserRepository $users, Flusher $flusher, PasswordHasher $hasher)
    {
        $this->users = $users;
        $this->flusher = $flusher;
        $this->hasher = $hasher;
    }

    public function handle(Command $command): void
    {
        if (!$user = $this->users->findByResetToken($command->token)) {
            throw new \DomainException('Incorrect or confirmed token.');
        }
        $user->passwordReset(new \DateTimeImmutable(), $this->hasher->hash($command->password));

        $this->flusher->flush();
    }
}