<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;

class RegistrationType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('username', TextType::class, [
                'error_bubbling' => true,
            ])
            ->add('mail', EmailType::class, [
                'error_bubbling' => true,
            ])
            ->add('password', RepeatedType::class, [
                'error_bubbling' => true,
                'type' => PasswordType::class,
                'mapped' => false,
                'invalid_message' => 'Password does not match',
                'first_options'  => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat Password']])
            ->add('tos', CheckboxType::class, [
                'error_bubbling' => true,
                'mapped' => false,
                'constraints' => new IsTrue(),
                'invalid_message' => 'You must accept TOS',
                'label' => 'Accept Terms of Service',
                'label_attr' => ['class' => 'inline-label']
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => User::class
        ]);
    }
}