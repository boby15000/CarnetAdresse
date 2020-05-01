<?php

namespace App\Form;


use Beelab\Recaptcha2Bundle\Form\Type\RecaptchaSubmitType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;

class MotDePasseOublieType extends AbstractType
{
    

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label_attr' => [ 'class' => 'font-weight-bold'
                ],
                'attr' => [
                    'placeholder' => "Votre email",
                    'class' => 'form-control-lg'
                ],
                'required' => true,
                'label' => 'Email de votre compte',
                'constraints' => [
                    new Email(['message' => "L'email '{{ value }}' n'est pas valide."])
                ]
            ])
            ->add('captcha', RecaptchaSubmitType::class, [
                'error_bubbling' => true,
                'label' => 'Save'
            ])
            ->add('save', SubmitType::class, [
                'label' => "J'ai oublie mon mot de passe"
            ]);
    }




    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
           
        ]);
    }


}