<?php

namespace App\DataFixtures;

use App\DataFixtures\UserFixtures;
use App\Entity\Fiche;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Persistence\ManagerRegistry;
use Faker\Factory;

class FicheFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    

    
    public function load(ObjectManager $manager)
    {

        $users = $manager->getRepository(User::class)->findAll();
        $faker = Factory::create('fr_FR');

        foreach($users as $user) 
        {
            if ( $user->isCompteactif() )
            {
                for ($i=0; $i < 100 ; $i++) 
                {
                    $fiche = new Fiche();
                    $fiche
                        ->setProfessionel(0)
                        ->setNom($faker->lastName())
                        ->setPrenom($faker->firstName())
                        ->setAdresse($faker->address())
                        ->setTelFixe($faker->phoneNumber())
                        ->setTelPortable($faker->phoneNumber())
                        ->setEmail($faker->email())
                        ->setUser($user);

                    $manager->persist($fiche);            
                }
            }
        }
        $manager->flush();
    }


    public function getDependencies()
    {
        return array (
            UserFixtures::class,
        );
    }


    public static function getGroups():array
    {
        return ['group2'];
    }

}
