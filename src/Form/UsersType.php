<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType; 
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class UsersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('email', TextType::class)
            // ->add('roles', ChoiceType::class, [
            //     'choices' => [
            //         'ROLE_ADMIN' => 'ROLE_ADMIN',
            //         'ROLE_USER' => 'ROLE_USER',
            //         'ROLE_SECRETARY' => 'ROLE_SECRETARY',
            //         'ROLE_TREASURER' => 'ROLE_TREASURER',
            //         'ROLE_MEMBER' => 'ROLE_MEMBER',
            //         'ROLE_VICE_ADMIN' => 'ROLE_VICE_ADMIN',
            //     ],
            //     'multiple' => true,
            //     'expanded' => true,
            // ])
            // ->add('plainPassword', PasswordType::class, [
            //     'mapped' => false,
            //     'required' => false,
            // ])
            // ->add('firstName', TextType::class)
            // ->add('lastName', TextType::class)
            // ->add('phoneNumber', TextType::class)
            // ->add('isSubscribed')
            // ->add('nextOfKins', TextType::class)
            // ->add('nextOfKinTel', TextType::class)
            // ->add('registrationAmount')
            // ->add('updatedAt', DateTimeType::class, [
            //     'widget' => 'single_text',
            // ])
            ->add('profilePicture', FileType::class, [
                'label' => 'Profile Picture (JPEG, PNG files only)',
                'required' => false,
                'data_class' => null, // Allows string to be used in the form
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid JPEG or PNG image',
                    ])
                ],
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
