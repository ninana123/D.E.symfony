<?php

namespace App\Model\User\Entity\User;

class Email
{
    private string $value;

    public function __construct(string $value)
    {
        if(!filter_var($value,FILTER_VALIDATE_EMAIL)){
            throw new \InvalidArgumentException('Incorrect Email');
        }

        $this->value = mb_strtolower($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }
}