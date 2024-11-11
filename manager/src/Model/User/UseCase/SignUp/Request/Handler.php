<?php

namespace App\Model\User\UseCase\SignUp\Request;


use App\Model\Flusher;
use App\Model\User\Entity\Email;
use App\Model\User\Entity\Id;
use App\Model\User\Entity\User;
use App\Model\User\Entity\UserRepository;
use App\Model\User\Service\ConfirmTokenizer;
use App\Model\User\Service\ConfirmTokenSender;
use App\Model\User\Service\PasswordHasher;
use Doctrine\ORM\EntityManagerInterface;

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

        $user = new User(Id::next(), $email, $this->hasher->hash($command->password), $token = $this->confirmTokenizer->generate(), new \DateTimeImmutable());

        $this->users->add($user);

        $this->sender->send($email, $token);

        $this->flusher->flush();
    }
}