<?php

namespace App\Repository;

use App\Entity\DemandeByMail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DemandeByMail|null find($id, $lockMode = null, $lockVersion = null)
 * @method DemandeByMail|null findOneBy(array $criteria, array $orderBy = null)
 * @method DemandeByMail[]    findAll()
 * @method DemandeByMail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DemandeByMailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DemandeByMail::class);
    }

    // /**
    //  * @return DemandeByMail[] Returns an array of DemandeByMail objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DemandeByMail
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
