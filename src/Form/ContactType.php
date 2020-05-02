<?php

namespace App\Form;


use Beelab\Recaptcha2Bundle\Form\Type\RecaptchaSubmitType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactType extends AbstractType
{
    

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('de', EmailType::class, [
                'label_attr' => [ 'class' => 'font-weight-bold'
                ],
                'attr' => [
                    'placeholder' => "Votre email",
                    'class' => 'form-control-lg'
                ],
                'help' => "Seulement si vous souhaitez une réponse.",
                'required' => false,
                'label' => 'De'
            ])
            ->add('message',  TextareaType::class, [
                'label_attr' => [ 'class' => 'font-weight-bold'
                ],
                'attr' => [
                    'placeholder' => "Votre message",
                    'class' => 'form-control-lg',
                    'maxlength' => 1000
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le message doit être renseigné.']),
                    new Length(
                        ['min' => 10, 'minMessage' => 'Le message doit comporté minimum {{ limit }} caractères',
                        'max' => 1000, 'maxMessage' => 'Le message doit comporté maximum {{ limit }} caractères'
                    ]),
                ],
                'required' => true,
                'label' => 'Votre message'               
            ])
            ->add('captcha', RecaptchaSubmitType::class, [
                'error_bubbling' => true,
                'label' => 'Save'
            ])
            ->add('save', SubmitType::class, [
                'label' => "Envoyer"
            ]);
    }




    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
           
        ]);
    }


}
