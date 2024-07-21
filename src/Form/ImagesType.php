<?php

namespace App\Form;

use App\Entity\GroupMessage;
use App\Entity\Images;
use App\Entity\PrivateMessage;
use App\Entity\Users;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImagesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fileName')
            ->add('size')
            ->add('uploadedAt', null, [
                'widget' => 'single_text',
            ])
            ->add('groupImages', EntityType::class, [
                'class' => GroupMessage::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('privateImages', EntityType::class, [
                'class' => PrivateMessage::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('usersProfileImage', EntityType::class, [
                'class' => Users::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Images::class,
        ]);
    }
}
