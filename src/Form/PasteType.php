<?php

namespace App\Form;

use App\Entity\Paste;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PasteType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('content', TextareaType::class, [
                'error_bubbling' => true,
                'required' => true,
            ])
            ->add('title', TextType::class, [
                'error_bubbling' => true,
                'required' => false,
                'label' => 'Title:'
            ])
            ->add('syntax', ChoiceType::class, [
                'label' => 'Syntax:',
                'choices' => [
                    'None' => 'None',
                    'Java' => 'Java',
                    'Go' => 'Go',
                    'PHP' => 'PHP'
                ]
            ])
            ->add('visibility', ChoiceType::class, [
                'label' => 'Visibility:',
                'choices' => [
                    'Public' => 1,
                    'Unlisted' => 2,
                    'Private (Account needed)' => 3
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => Paste::class
        ]);
    }
}