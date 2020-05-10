<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtensionDate extends AbstractExtension
{
    
   Private $moisFr = ['','Janvier', 'Férvier','Mars','Avril','Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];



    public function getFilters()
    {
        return [
            new TwigFilter('DateToString', [$this, 'Convertir']),
        ];
    }



    public function Convertir($date)
    {
        $annee = (new \DateTime())->format('Y');
        $mois = $date->format('n');
        $jour = $date->format('d');


        return $jour .' '. $this->moisFr[$mois] . ' ' . $annee ;
    }
}