<?php

namespace App\Model\User\UseCase\SignUp\Request;


use App\Model\Flusher;
use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\User;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Service\ConfirmTokenizer;
use App\Model\User\Service\ConfirmTokenSender;
use App\Model\User\Service\PasswordHasher;

class Handler
{

    private UserRepository $users;
    private PasswordHasher $hasher;
    private ConfirmTokenizer $confirmTokenizer;
    private ConfirmTokenSender $sender;
    private Flusher $flusher;

    public function __construct(UserRepository $users, PasswordHasher $hasher, Flusher $flusher, ConfirmTokenizer $confirmTokenizer, ConfirmTokenSender $sender)
    {
        $this->users = $users;
        $this->hasher = $hasher;
        $this->flusher = $flusher;
        $this->confirmTokenizer = $confirmTokenizer;
        $this->sender = $sender;
    }

    public function handle(Command $command): void
    {
        $email = new Email($command->email);

        if ($this->users->hasByEmail($email)) {
            throw new \DomainException('User already exists');
        }

        $user = new User(Id::next(), new \DateTimeImmutable());

        $user->signUpByEmail($email, $this->hasher->hash($command->password), $token = $this->confirmTokenizer->generate());
        $this->users->add($user);

        $this->sender->send($email, $token);

        $this->flusher->flush();
    }
}