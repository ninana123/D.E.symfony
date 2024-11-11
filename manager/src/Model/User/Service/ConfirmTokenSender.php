<?php

namespace App\Model\User\Service;

use App\Model\User\Entity\Email;

interface ConfirmTokenSender
{
    public function send(Email $email, string $token): void;
}