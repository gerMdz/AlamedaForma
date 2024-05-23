<?php

namespace App\Form;

use App\Entity\Inicio;
use App\Entity\Organization;
use Eckinox\TinymceBundle\Form\Type\TinymceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InicioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('content', TinymceType::class, [
                'attr' => ["toolbar" => "bold italic underline | bullist numlist",],
            ])
            ->add('terms')
            ->add('createdAt', null, [
                'widget' => 'single_text',
            ])
            ->add('updateAt', null, [
                'widget' => 'single_text',
            ])
            ->add('organization', EntityType::class, [
                'class' => Organization::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Inicio::class,
        ]);
    }
}
