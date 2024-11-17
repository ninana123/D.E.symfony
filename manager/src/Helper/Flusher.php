<?php

namespace App\Helper;

use Doctrine\ORM\EntityManagerInterface;

class Flusher implements \App\Model\Flusher
{
private EntityManagerInterface $entityManager;
public function __construct(\Doctrine\ORM\EntityManagerInterface $entityManager)
{
    $this->entityManager = $entityManager;
}

    public function flush(): void
    {
        $this->entityManager->flush();
    }
}