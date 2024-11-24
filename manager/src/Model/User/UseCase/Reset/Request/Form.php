<?php

namespace App\Model\User\UseCase\Reset\Request;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\EmailType;
use App\Model\User\UseCase\Reset\Reset\Command;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class);
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Command::class
        ]);
    }

}