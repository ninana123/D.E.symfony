<?php

namespace App\Model\User\Service;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\ResetToken;
use Twig\Environment;

class ResetTokenSender
{
    private Environment $twig;
    private \Swift_Mailer $mailer;
    private array $from;

    public function __construct(Environment $twig, array $from, \Swift_Mailer $mailer)
    {
        $this->twig = $twig;
        $this->from = $from;
        $this->mailer = $mailer;
    }

    public function send(Email $email, ResetToken $token): void
    {
        $message = (new \Swift_Message('Password resetting'))
            ->setFrom($this->from)
            ->setTo($email->getValue())
            ->setBody($this->twig->render('mail/user/reset.html.twig', [
                'token' => $token->getToken(),
                'text/html'
            ]));

        if (!$this->mailer->send($message)) {
            throw new \RuntimeException('Unable to send Message');
        }
    }
}