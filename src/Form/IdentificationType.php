<?php

namespace App\Form;

use Beelab\Recaptcha2Bundle\Form\Type\RecaptchaSubmitType;
use Beelab\Recaptcha2Bundle\Form\Type\RecaptchaType;
use Beelab\Recaptcha2Bundle\Validator\Constraints\Recaptcha2;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;



class IdentificationType extends AbstractType
{

  

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label_attr' => [ 'class' => 'font-weight-bold'
                ],
                'attr' => [
                    'class' => 'form-control-lg'
                ],
                'error_bubbling' => true,
                'label' => 'Votre Identifiant (email)'
            ])
            ->add('password', PasswordType::class, [
                'label_attr' => [ 'class' => 'font-weight-bold'
                ],
                'attr' => [
                    'class' => 'form-control-lg'
                ],
                'error_bubbling' => true,
                'label' => 'Votre Mot de passe'
            ])
            ->add('captcha', RecaptchaSubmitType::class, [
                'error_bubbling' => true,
                'label' => 'Save'
            ])
            ->add('save', SubmitType::class, [
                'label' => "Se connecter"
            ]);
    }
    


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
           
        ]);
    }

}
