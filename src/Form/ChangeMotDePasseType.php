<?php

namespace App\Form;

use App\Entity\User;
use Beelab\Recaptcha2Bundle\Form\Type\RecaptchaSubmitType;
use Beelab\Recaptcha2Bundle\Form\Type\RecaptchaType;
use Beelab\Recaptcha2Bundle\Validator\Constraints\Recaptcha2;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue as RecaptchaTrue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangeMotDePasseType extends AbstractType
{
    
    const ROUTE_CHANGEPASSWORD = "";

    private $request_stack;


    public function __construct(RequestStack $request_stack)
    {
        $this->request_stack = $request_stack;
    }

  

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
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
                'label' => "Enregistrer le nouveau mot de passe"
            ]);


            if (  $this->request_stack->getMasterRequest()->attributes->get('_route') === self::ROUTE_CHANGEPASSWORD )
            {

               $builder->add('motdepasse', PasswordType::class, [
                    'label_attr' => [ 'class' => 'font-weight-bold'
                    ],
                    'attr' => [
                        'placeholder' => "",
                        'class' => 'form-control-lg'
                    ],
                    'required' => true,
                    'label' => 'Votre mot de passe actuel',
                    ]
                ])
            }       

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
           
        ]);
    }
}
