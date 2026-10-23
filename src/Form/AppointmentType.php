<?php

namespace App\Form;

use App\Entity\Appointment;
use App\Entity\Kid;
use App\Repository\KidRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AppointmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('active')
            ->add('startAt', null, [
                'widget' => 'single_text'
            ])
            ->add('endAt', null, [
                'widget' => 'single_text'
            ])
            ->add('attendees', EntityType::class, [
                'class' => Kid::class,
                'choice_label' => fn(Kid $kid) => $kid->getName(),
                'expanded' => true,
                'group_by' => fn(Kid $kid) => $kid->getKidGroup()?->getName(),
                'multiple' => true,
                'required' => false,
                'query_builder' => fn(KidRepository $repo) => $repo->createQueryBuilder('kid')
                    ->leftJoin('kid.kidGroup', 'kid_group')
                    ->addSelect('kid_group')
                    ->andWhere('kid.active = true')
                    ->orderBy('kid_group.name')
                    ->addOrderBy('kid.name')
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Appointment::class,
        ]);
    }
}
