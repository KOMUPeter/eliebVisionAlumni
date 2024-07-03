<?php

namespace App\Form;

use App\Entity\GroupMessage;
use App\Entity\Images;
use App\Entity\Users;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UsersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('roles')
            ->add('password')
            ->add('firstName')
            ->add('lastName')
            ->add('phoneNumber')
            ->add('registrationDate', null, [
                'widget' => 'single_text',
            ])
            ->add('isSubscribed')
            ->add('groupRecipient', EntityType::class, [
                'class' => GroupMessage::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('profileImage', EntityType::class, [
                'class' => Images::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
