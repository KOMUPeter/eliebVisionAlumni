<?php

namespace App\Form;

use App\Entity\Images;
use App\Entity\PrivateMessage;
use App\Entity\Users;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PrivateMessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content')
            ->add('sentAt', null, [
                'widget' => 'single_text',
            ])
            ->add('sender', EntityType::class, [
                'class' => Users::class,
                'choice_label' => 'id',
            ])
            ->add('recipient', EntityType::class, [
                'class' => Users::class,
                'choice_label' => 'id',
            ])
            ->add('privateImages', EntityType::class, [
                'class' => Images::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PrivateMessage::class,
        ]);
    }
}
