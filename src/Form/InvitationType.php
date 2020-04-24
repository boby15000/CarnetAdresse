<?php

namespace App\Form;

use App\Entity\Invitation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class InvitationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       
        $builder
            ->add('Destinataires', TextareaType::class, [
                'attr' => 
                    ['placeholder' => "julien.durand@free.fr;maurice.jean@gmail.fr"]
                ])
            ->add('message', TextareaType::class, [
                'attr' => 
                    ['placeholder' => $options['messageEmail'] ],
                'mapped' => false,
                'required'   => false
                ])
            ->add('Envoyer', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Invitation::class,
            'messageEmail' => '',
        ]);

        $resolver->setAllowedTypes('messageEmail', 'string');
    }
}
