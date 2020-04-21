<?php

namespace App\Form;

use App\Entity\DemandeByMail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class DemandeByMailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $message = $options['nomComplet'] ." à besoin de vous pour remplir vos informations suivantes : Adresse postale, email, téléphone.";

        $builder
            ->add('Destinataires', TextareaType::class)
            ->add('message', TextareaType::class, [
                'mapped' => false,
                'data' => $message
                ])
            ->add('Envoyer', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DemandeByMail::class,
            'nomComplet' => '',
        ]);

        $resolver->setAllowedTypes('nomComplet', 'string');
    }
}
