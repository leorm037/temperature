<?php

/*
 * This file is part of Temperature.
 *
 * (c) Leonardo Rodrigues Marques <leonardo@rodriguesmarques.com.br>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Repository;

use App\Entity\Temperature;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Temperature>
 *
 * @method Temperature|null find($id, $lockMode = null, $lockVersion = null)
 * @method Temperature|null findOneBy(array $criteria, array $orderBy = null)
 * @method Temperature[]    findAll()
 * @method Temperature[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TemperatureRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Temperature::class);
    }

    public function save(Temperature $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Temperature $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Temperature[]
     */
    public function findByDays(int $days)
    {
        $date = new \DateTime();
        $date->sub(\DateInterval::createFromDateString("{$days} day"));
        $dateFormat = $date->format('Y-m-d H:i:s');

        return $this->createQueryBuilder('t')
                        ->select('t.dateTime, t.cpu, t.gpu, t.sensation, t.temperature')
                        ->where('t.dateTime >= :date')
                        ->setParameter('date', $dateFormat)
                        ->orderBy('t.dateTime', 'DESC')
                        ->getQuery()
                        ->enableResultCache(300)
                        ->getResult()
        ;
    }

    public function findByDaysGroup(int $days)
    {
        $date = new \DateTime();
        $date->sub(\DateInterval::createFromDateString("{$days} day"));
        $dateFormat = $date->format('Y-m-d H:i:s');

        return $this->createQueryBuilder('t')
                        ->select("DATE_FORMAT(t.dateTime, '%Y-%m-%d %H:00:00') AS dateTime, AVG(t.cpu) AS cpu, AVG(t.gpu) AS gpu, AVG(t.sensation) AS sensation, AVG(t.temperature) AS temperature")
                        ->where('t.dateTime >= :date')
                        ->setParameter('date', $dateFormat)
                        ->addGroupBy("dateTime")
                        ->orderBy('dateTime', 'DESC')
                        ->getQuery()
                        ->enableResultCache(300)
                        ->getResult()
        ;
    }

    public function findByDate(\DateTime $date): ?Temperature
    {
        $dateFormat = $date->format('Y-m-d H:i:s');

        return $this->createQueryBuilder('t')
                        ->where('t.dateTime = :date')
                        ->setParameter('date', $dateFormat)
                        ->setMaxResults(1)
                        ->getQuery()
                        ->enableResultCache(3600)
                        ->getOneOrNullResult()
        ;
    }

    public function lastTemperature(): float
    {
        return $this->createQueryBuilder('t')
                        ->select('t.temperature')
                        ->setMaxResults(1)
                        ->orderBy('t.dateTime', 'DESC')
                        ->getQuery()
                        ->enableResultCache(300)
                        ->getSingleScalarResult()
        ;
    }

    //    /**
    //     * @return Temperature[] Returns an array of Temperature objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }
    //    public function findOneBySomeField($value): ?Temperature
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
