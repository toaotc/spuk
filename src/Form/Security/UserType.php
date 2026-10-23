<?php

namespace App\Form\Security;

use App\Entity\Security\Roles;
use App\Entity\Security\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username')
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Appointments' => [
                        Roles::CREATE_APPOINTMENT => Roles::CREATE_APPOINTMENT,
                        Roles::READ_APPOINTMENT => Roles::READ_APPOINTMENT,
                        Roles::UPDATE_APPOINTMENT => Roles::UPDATE_APPOINTMENT,
                        Roles::DELETE_APPOINTMENT => Roles::DELETE_APPOINTMENT,
                    ],
                    'Guardians' => [
                        Roles::CREATE_GUARDIAN => Roles::CREATE_GUARDIAN,
                        Roles::READ_GUARDIAN => Roles::READ_GUARDIAN,
                        Roles::UPDATE_GUARDIAN => Roles::UPDATE_GUARDIAN,
                        Roles::DELETE_GUARDIAN => Roles::DELETE_GUARDIAN,
                    ],
                    'Kids' => [
                        Roles::CREATE_KID => Roles::CREATE_KID,
                        Roles::READ_KID => Roles::READ_KID,
                        Roles::UPDATE_KID => Roles::UPDATE_KID,
                        Roles::DELETE_KID => Roles::DELETE_KID,
                    ],
                    'Kid Groups' => [
                        Roles::CREATE_KID_GROUP => Roles::CREATE_KID_GROUP,
                        Roles::READ_KID_GROUP => Roles::READ_KID_GROUP,
                        Roles::UPDATE_KID_GROUP => Roles::UPDATE_KID_GROUP,
                        Roles::DELETE_KID_GROUP => Roles::DELETE_KID_GROUP,
                    ],
                    'Users' => [
                        Roles::CREATE_USER => Roles::CREATE_USER,
                        Roles::READ_USER => Roles::READ_USER,
                        Roles::UPDATE_USER => Roles::UPDATE_USER,
                        Roles::DELETE_USER => Roles::DELETE_USER,
                    ]
                ],
                'expanded' => true,
                'multiple' => true,
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
