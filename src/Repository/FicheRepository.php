<?php

namespace App\Repository;

use App\Entity\Fiche;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Fiche|null find($id, $lockMode = null, $lockVersion = null)
 * @method Fiche|null findOneBy(array $criteria, array $orderBy = null)
 * @method Fiche[]    findAll()
 * @method Fiche[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FicheRepository extends ServiceEntityRepository
{
    

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fiche::class);
    }

   
    /**
     * Retourne les données l'user spécifié "ID" et de la lettre 
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public function findAllByUserAndLetter($user, $letter = 'A')
    {
        return $this->createQueryBuilder('f')
            ->Where('f.nom LIKE :letter')
            ->orWhere('f.libelle LIKE :letter')
            ->setParameter('letter', $letter.'%')
            ->andWhere('f.user = :user')
            ->setParameter('user', $user)
            ->orderBy('f.nom', 'ASC')
            ->orderBy('f.libelle', 'ASC')
            ->getQuery()->getResult();
    }







    // /**
    //  * @return Fiche[] Returns an array of Fiche objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Fiche
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
