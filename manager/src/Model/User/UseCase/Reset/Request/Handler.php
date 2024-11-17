<?php

namespace App\Model\User\UseCase\Reset\Request;


use App\Model\Flusher;
use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Service\ResetTokenizer;
use App\Model\User\Service\ResetTokenSender;

class Handler
{

    private UserRepository $users;
    private ResetTokenizer $tokenizer;
    private ResetTokenSender $sender;
    private Flusher $flusher;

    public function __construct(UserRepository $users, Flusher $flusher, ResetTokenizer $tokenizer, ResetTokenSender $sender)
    {
        $this->users = $users;
        $this->flusher = $flusher;
        $this->tokenizer = $tokenizer;
        $this->sender = $sender;
    }

    public function handle(Command $command): void
    {
        $user = $this->users->getByEmail(new Email($command->email));

        $user->requestPasswordReset($this->tokenizer->generate(), new \DateTimeImmutable());

        $this->flusher->flush();

        $this->sender->send($user->getEmail(), $user->getResetToken());
    }
}