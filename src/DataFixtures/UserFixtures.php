<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UserFixtures extends Fixture implements FixtureGroupInterface
{

	 public const PASSWORD_DEFAUT = '2a3z4e5r';
     public const ADMIN_USER_REFERENCE = 'admin-user' ;

     private $passwordEncoder;

     public function __construct(UserPasswordEncoderInterface $passwordEncoder)
     {
        $this->passwordEncoder = $passwordEncoder;
     }



    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        for ($i=0; $i < 20 ; $i++) {
           $user = new User();
           $user->setNom($faker->lastName())
                ->setPrenom($faker->firstName())
                ->setEmail($faker->email())
                ->setMotdepasse( $this->passwordEncoder->EncodePassword($user, self::PASSWORD_DEFAUT) )
                ->setCompteactif($faker->boolean())
                ->setCreerle($faker->dateTimeBetween('-20 days',new \DateTime('2020-05-03')));

            $manager->persist($user);
        }

        $manager->flush();
    }



    public static function getGroups():array
    {
        return ['group1'];
    }


    /*
        $user = new User($this->passwordEncoder);
        $user
            ->setNom('Fourgheon')
            ->setPrenom('Nicolas')
            ->setEmail("boby15000@hotmail.com")
            ->setPassword('2A3z4e5r*')
            ->setRoles(['ROLE_ADMIN']);

        $manager->persist($user);

        $manager->flush();

        $this->addReference( self::ADMIN_USER_REFERENCE, $user );
    */

}
