<?php

namespace App\Form;

use App\Entity\User;
use Beelab\Recaptcha2Bundle\Form\Type\RecaptchaType;
use Beelab\Recaptcha2Bundle\Validator\Constraints\Recaptcha2;
use Beelab\Recaptcha2Bundle\Form\Type\RecaptchaSubmitType;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue as RecaptchaTrue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'label_attr' => [ 'class' => 'font-weight-bold'
                ],
                'attr' => [
                    'placeholder' => "ex: Durand",
                    'class' => 'form-control-lg'
                ],
                'required' => true
            ])
            ->add('prenom', TextType::class, [
                'label_attr' => [ 'class' => 'font-weight-bold'
                ],
                'attr' => [
                    'placeholder' => "ex: Martin",
                    'class' => 'form-control-lg'
                ],
                'required' => true,
                'label' => 'Prénom'               
            ])
            ->add('email', EmailType::class, [
                'label_attr' => [ 'class' => 'font-weight-bold'
                ],
                'attr' => [
                        'placeholder' => "ex: martin.durand@gmail.fr",
                        'class' => 'form-control-lg'
                ],
                'required' => true,
                'help' => "L'adresse email doit être valide pour activer le compte."
            ])
            ->add('motdepasse', RepeatedType::class, [
                'options' => ['attr' => ['class' => 'form-control-lg'],
                            'label_attr' => [ 'class' => 'font-weight-bold'],
                ],
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent être identique.',
                'first_options'  => [
                    'label' => 'Mot de passe',
                    'help' => "Le mot de passe doit comporter minimum 5 caractères",
                    ],
                'second_options' => ['label' => 'Confirmez votre mot de passe'],
                'required' => true
            ])
            ->add('captcha', RecaptchaSubmitType::class, [
                'error_bubbling' => true,
                'label' => 'Save'
            ])
            ->add('save', SubmitType::class, [
                'label' => "S'incrire"
            ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
