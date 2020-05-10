<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
     public function getFunctions()
    {
        return [
            new TwigFunction('age', [$this, 'CalculAge']),
        ];
    }


    public function CalculAge($date)
    {
        //$datetime1 = new \DateTime('2009-10-11');
        $Aujourdhui = new \DateTime();
        $interval = $date->diff($Aujourdhui);
        return $interval->format('%r%y ans');
        
    }

  
}