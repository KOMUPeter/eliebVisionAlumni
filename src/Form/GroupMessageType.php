<?php

namespace App\Form;

use App\Entity\GroupMessage;
use App\Entity\Images;
use App\Entity\Users;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroupMessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('groupContent')
            ->add('groupSender', EntityType::class, [
                'class' => Users::class,
                'choice_label' => 'id',
            ])
            ->add('groupRecipient', EntityType::class, [
                'class' => Users::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('groupImages', EntityType::class, [
                'class' => Images::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GroupMessage::class,
        ]);
    }
}
