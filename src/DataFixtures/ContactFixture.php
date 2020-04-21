<?php

namespace App\DataFixtures;

use App\DataFixtures\UserFixtures;
use App\Entity\Contact;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Persistence\ManagerRegistry;
use Faker\Factory;

class ContactFixture extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{

    public function load(ObjectManager $manager)
    {

        $faker = Factory::create('fr_FR');

    	for ($i=0; $i < 200 ; $i++) {
    		$contact = new Contact();
    		$contact
    			->setProfessionel($faker->boolean())
    			->setNom($faker->lastName())
				->setPrenom($faker->firstName())
				->setAdresse($faker->address())
				->setTel($faker->phoneNumber())
				->setEmail($faker->email())
                ->setPrivateKey($this->getReference( UserFixtures::ADMIN_USER_REFERENCE )->getKeyPrivate());

            $manager->persist($contact);
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
