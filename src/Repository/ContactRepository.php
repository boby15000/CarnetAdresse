<?php

namespace App\Repository;

use App\Entity\Contact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Contact|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contact|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contact[]    findAll()
 * @method Contact[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contact::class);
    }


    /**
     * [findAllByKey description]
     * @param  [type] $value [description]
     * @return Query
     */
    public function findAllByKeyAndLetter($letter, $keyPrivate)
    {
        return $this->createQueryBuilder('Ctt')
            ->Where('Ctt.Nom LIKE :letter')
            ->setParameter('letter', $letter.'%')
            ->andWhere('Ctt.PrivateKey = :PrivateKey')
            ->setParameter('PrivateKey', $keyPrivate)
            ->orderBy('Ctt.Nom', 'ASC')
            ->getQuery();
    }

    /**
     * [findAllByKey description]
     * @param  [type] $value [description]
     * @return Query
     */
    public function findAllByKeyOnly($keyPrivate)
    {
        return $this->createQueryBuilder('Ctt')
            ->select('COUNT(Ctt)')
            ->Where('Ctt.PrivateKey = :PrivateKey')
            ->setParameter('PrivateKey', $keyPrivate)
            ->getQuery()
            ->getSingleScalarResult();
    }



    // /**
    //  * @return Adresse[] Returns an array of Adresse objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Adresse
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
