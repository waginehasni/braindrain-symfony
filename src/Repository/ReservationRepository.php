<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Reservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reservation[]    findAll()
 * @method Reservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }
    public function getMonth()
    {

        $qb = $this->createQueryBuilder('v')
            ->select('COUNT(v.numreservation) AS post, SUBSTRING(v.date, 1, 7) AS month')
            ->groupBy('month');
        return $qb->getQuery()
            ->getResult();
    }
    public function getYear()
    {

        $qb = $this->createQueryBuilder('v')
            ->select('COUNT(v.numreservation) AS post, SUBSTRING(v.date, 1, 4) AS year')
            ->groupBy('year');
        return $qb->getQuery()
            ->getResult();
    }
    public function getDay()
    {

        $qb = $this->createQueryBuilder('v')
            ->select('COUNT(v.numreservation) AS post, SUBSTRING(v.date, 1, 10) AS day')
            ->groupBy('day');
        return $qb->getQuery()
            ->getResult();
    }



    // /**
    //  * @return Reservation[] Returns an array of Reservation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Reservation
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

}
