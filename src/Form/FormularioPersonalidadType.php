<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormularioPersonalidadType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $optionChoices = [
            '1' => 1,
            '2' => 2,
            '3' => 3,
            '4' => 4,
        ];

        $personalidades = $options['personalidades'];


        foreach ($personalidades as $personalidad) {

            $builder->add($personalidad->getId() . substr($personalidad->getD(), 0, 1), ChoiceType::class, [
                'label' => $personalidad->getD(),
                'choices' => $optionChoices
            ]);
            $builder->add($personalidad->getId() . substr($personalidad->getI(), 0, 1), ChoiceType::class, [
                'label' => $personalidad->getI(),
                'choices' => $optionChoices
            ]);
            $builder->add($personalidad->getId() . substr($personalidad->getS(), 0, 1), ChoiceType::class, [
                'label' => $personalidad->getS(),
                'choices' => $optionChoices
            ]);
            $builder->add($personalidad->getId() . substr($personalidad->getC(), 0, 1), ChoiceType::class, [
                'label' => $personalidad->getC(),
                'choices' => $optionChoices
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'personalidades' => null
        ]);
    }
}
