<?php

namespace App\Form;

use App\Entity\Payout;
use App\Entity\Users;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PayoutType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('amount')
            ->add('month', DateType::class, [
                'label' => 'Month',
                'widget' => 'single_text',
                'required' => false,
                'attr' => ['placeholder' => 'Select month']
            ])
            ->add('user', EntityType::class, [
                'class' => Users::class,
                'choice_label' => function (Users $user) {
                    return $user->getFirstName() . ' ' . $user->getLastName() . '';
                },
                'label' => 'User',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Payout::class,
        ]);
    }
}
