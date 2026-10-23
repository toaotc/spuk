<?php

namespace App\Form;

use App\Entity\Appointment;
use App\Entity\Guardian;
use App\Entity\Kid;
use App\Entity\KidGroup;
use App\Repository\GuardianRepository;
use App\Repository\KidGroupRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class KidType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('active')
            ->add('kidGroup', EntityType::class, [
                'class' => KidGroup::class,
                'choice_label' => 'name',
            ])
            ->add('guardians', EntityType::class, [
                'by_reference' => false,
                'class' => Guardian::class,
                'choice_label' => 'name',
                'expanded' => true,
                'group_by' => fn(Guardian $guardian) => sprintf('%s…', $guardian->getName()[0]),
                'multiple' => true,
                'query_builder' => fn(GuardianRepository $repo) => $repo->createQueryBuilder('o')
                    ->andWhere('o.active = true')
                    ->orderBy('o.name'),
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Kid::class,
        ]);
    }
}
