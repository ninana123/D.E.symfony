<?php

namespace App\Model\User\UseCase\Reset\Request;


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
    private ConfirmTokenizer $confirmTokenizer;
    private ConfirmTokenSender $sender;
    private Flusher $flusher;

    public function __construct(UserRepository $users, Flusher $flusher, ConfirmTokenizer $confirmTokenizer, ConfirmTokenSender $sender)
    {
        $this->users = $users;
        $this->flusher = $flusher;
        $this->confirmTokenizer = $confirmTokenizer;
        $this->sender = $sender;
    }

    public function handle(Command $command): void
    {
        $user = $this->users->getByEmail(new Email($command->email));

        $user->requestPasswordReset($this->confirmTokenizer->generate(), new \DateTimeImmutable());

        $this->flusher->flush();

        $this->sender->send($user->getEmail(), $user->getResetToken());
    }
}