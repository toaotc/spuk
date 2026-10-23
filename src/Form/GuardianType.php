<?php

namespace App\Form;

use App\Entity\Guardian;
use App\Entity\Kid;
use App\Repository\KidRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GuardianType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('active')
            ->add('kids', EntityType::class, [
                'attr' => ['size' => 10],
                'class' => Kid::class,
                'choice_label' => fn(Kid $kid) => $kid->getName(),
                'group_by' => fn(Kid $kid) => $kid->getKidGroup()?->getName(),
                'expanded' => true,
                'multiple' => true,
                'query_builder' => fn(KidRepository $repo) => $repo->createQueryBuilder('o')
                    ->andWhere('o.active = true')
                    ->orderBy('o.name'),
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Guardian::class,
        ]);
    }
}
