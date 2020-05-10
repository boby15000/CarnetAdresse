<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtensionColor extends AbstractExtension
{
     public function getFunctions()
    {
        return [
            new TwigFunction('color', [$this, 'ColorStatus']),
        ];
    }


    public function ColorStatus($status)
    {
       switch ($status) {
           case 'Validé':
               return 'success';
               break;
            case 'Bloqué':
               return 'danger';
               break;
            case 'En Attente':
               return 'primary';
               break;
            case 'Erreur':
               return 'dark';
               break;     
       }
        
    }

  
}