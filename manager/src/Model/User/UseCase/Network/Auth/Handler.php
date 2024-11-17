<?php

namespace App\Model\User\UseCase\Network\Auth;


use App\Model\Flusher;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\User;
use App\Model\User\Entity\User\UserRepository;

class Handler
{
    private UserRepository $users;
    private Flusher $flusher;

    public function __construct(UserRepository $users, Flusher $flusher)
    {
        $this->users = $users;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        if ($this->users->hasByNetworkIdentify($command->network, $command->identify)) {
            throw new \DomainException('User already exists');
        }

        $user = new User(Id::next(), new \DateTimeImmutable());

        $user->signUpByNetwork($command->network, $command->identify);

        $this->users->add($user);
        $this->flusher->flush();
    }
}