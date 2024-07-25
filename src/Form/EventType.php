<?php

namespace App\Form;

use App\Entity\Events;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Event Title',
                'required' => true,
            ])
            ->add('description', TextType::class, [
                'label' => 'Event Description',
                'required' => true,
            ])
            ->add('eventDate', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Event Date',
                'required' => true,
            ])
            ->add('cost', null, [
                'label' => 'Event Cost',
                'required' => false,
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Disbursement' => 'Disbursement',
                    'GroupHappenings' => 'GroupHappenings',
                ],
                'label' => 'Event Type',
                'required' => true,
            ])
            ->add('personInvolved', TextType::class, [
                'label' => 'Person Involved',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Events::class,
        ]);
    }
}
