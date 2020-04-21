<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UserFixtures extends Fixture implements FixtureGroupInterface
{

	 public const ADMIN_USER_REFERENCE = 'admin-user' ;

     private $passwordEncoder;

     public function __construct(UserPasswordEncoderInterface $passwordEncoder)
     {
        $this->passwordEncoder = $passwordEncoder;
     }



    public function load(ObjectManager $manager)
    {
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

    }


    public static function getGroups():array
    {
        return ['group1'];
    }


}
