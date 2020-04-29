<?php

namespace App\Form;

use App\Entity\Fiche;
use Beelab\Recaptcha2Bundle\Form\Type\RecaptchaSubmitType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FicheType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('professionel', CheckboxType::class, [
                'label' => 'Cette fiche est professionnel',
                'label_attr' => [ 'class' => 'switch-custom font-weight-bold'
                ],
                'attr' => [
                    'class' => 'form-control-lg'
                ],
                'error_bubbling' => true,
                'required' => false
            ])
            ->add('libelle', TextType::class, [
                'label_attr' => [ 'class' => 'font-weight-bold'
                ],
                'attr' => [
                    'placeholder' => "Entreprise Drive&Cook",
                    'class' => 'form-control-lg',
                    'data-toggle' => 'popover',
                    'title' => 'Le Libellé',
                    'data-content' => 'Ce champ est obligatoire si cette fiche est professionel.'
                ],
                'error_bubbling' => true,
                'required' => false
            ])
            ->add('nom', TextType::class, [
                'label_attr' => [ 'class' => 'font-weight-bold'
                ],
                'attr' => [
                    'placeholder' => "Durand",
                    'class' => 'form-control-lg',
                    'data-toggle' => 'popover',
                    'title' => 'Le Nom',
                    'data-content' => "Ce champ est obligatoire si cette fiche n'est pas professionel."
                ],
                'error_bubbling' => true,
                'required' => false
            ])
            ->add('prenom', TextType::class, [
                'label_attr' => [ 'class' => 'font-weight-bold'
                ],
                'attr' => [
                    'placeholder' => "Martin",
                    'class' => 'form-control-lg',
                    'data-toggle' => 'popover',
                    'title' => 'Le Prénom',
                    'data-content' => "Ce champ est obligatoire si cette fiche n'est pas professionel."
                ],
                'error_bubbling' => true,
                'required' => false,
                'label' => 'Prénom'               
            ])
            ->add('dateDeNaissance', BirthdayType::class, [
                'label_attr' => [ 'class' => 'font-weight-bold'
                ],
                'placeholder' => [
                    'year' => 'Année', 'month' => 'Mois', 'day' => 'Jour',
                ],
                'attr' => [
                    'class' => ''
                ],
                'input' => 'datetime',
                'error_bubbling' => true,
                'required' => false,
                'label' => 'Date de Naissance'               
            ])
            ->add('adresse',  TextareaType::class, [
                'label_attr' => [ 'class' => 'font-weight-bold'
                ],
                'attr' => [
                    'placeholder' => "17 Rue Des Lilas 83570 Le Bourg",
                    'class' => 'form-control-lg'
                ],
                'error_bubbling' => true,
                'required' => true,
                'label' => 'Adresse Postal'               
            ])
            ->add('telPortable',  TelType::class, [
                'label_attr' => [ 'class' => 'font-weight-bold'
                ],
                'attr' => [
                    'placeholder' => "0632458149",
                    'class' => 'form-control-lg'
                ],
                'required' => false,
                'error_bubbling' => true,
                'label' => 'Téléphone Portable'               
            ])
            ->add('telFixe',  TelType::class, [
                'label_attr' => [ 'class' => 'font-weight-bold'
                ],
                'attr' => [
                    'placeholder' => "0483567891",
                    'class' => 'form-control-lg'
                ],
                'required' => false,
                'error_bubbling' => true,
                'label' => 'Téléphone Fixe'               
            ])
            ->add('email', EmailType::class, [
                'label_attr' => [ 'class' => 'font-weight-bold'
                ],
                'attr' => [
                        'placeholder' => "ex: martin.durand@gmail.fr",
                        'class' => 'form-control-lg'
                ],
                'error_bubbling' => true,
                'required' => true,
                'help' => "L'adresse email doit être valide pour activer le compte."
            ])
            ->add('captcha', RecaptchaSubmitType::class, [
                'error_bubbling' => true,
                'label' => 'Save'
            ])
            ->add('save', SubmitType::class, [

                'label' => "Enregistrer"
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Fiche::class,
        ]);
    }
}
