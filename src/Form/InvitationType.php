<?php

namespace App\Form;

use App\Entity\Contact;
use Beelab\Recaptcha2Bundle\Form\Type\RecaptchaSubmitType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class InvitationType extends AbstractType
{
    

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('destinataires', TextType::class, [
                'label_attr' => [ 'class' => 'font-weight-bold'
                ],
                'attr' => [
                    'placeholder' => "boby15000@hotmail.com;nicolas.fourgheon@gmail.com;durand.julien@hotmail.fr;alfred.levant@wanadoo.fr",
                    'class' => 'form-control-lg'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez renseigner les destinataires.']),
                    new Regex(
                        ['pattern' => "%^((([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5}))(;?))*$%",
                         'match' => true,
                         'message' => "le champ Destinataire n'est pas valide."
                    ])
                ],
                'required' => true,
                'label' => 'Destinataires'
            ])
            ->addEventListener(FormEvents::SUBMIT , [$this, 'onSubmit'])
            ->add('message',  TextareaType::class, [
                'label_attr' => [ 'class' => 'font-weight-bold'
                ],
                'attr' => [
                    'placeholder' => "Laissez vide pour utiliser le message par défaut.",
                    'class' => 'form-control-lg'
                ],
                'help' => "Cliquer sur ici pour voir le message par defaut",
                'help_attr' => [ 'class'=>'text-primary', 'data-toggle'=>"modal", 'data-target'=>"#messageModal"
                ],
                'required' => false,
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


    public function onSubmit(FormEvent $event)
    {
        $data = $event->getData();

        if ( $data['destinataires'] !== null )
        {
            $destinatairesArray = \explode( ";" , $data['destinataires'] ); 
            $data['destinataires'] = \array_filter($destinatairesArray);

            $event->setData($data); 
        }
        //dump($event->getData());
        //dump($event->getData()['destinataires']);
    }




    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            //'validation_groups' => true,
        ]);
    }

    
}
