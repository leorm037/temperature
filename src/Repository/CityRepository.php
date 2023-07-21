<?php

namespace App\Repository;

use App\Entity\City;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<City>
 *
 * @method City|null find($id, $lockMode = null, $lockVersion = null)
 * @method City|null findOneBy(array $criteria, array $orderBy = null)
 * @method City[]    findAll()
 * @method City[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CityRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, City::class);
    }

    public function save(City $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * 
     * @param string $country
     */
    public function findStatesByCountry(string $country)
    {
        return $this->createQueryBuilder('c')
                        ->select('c.state')
                        ->where('c.country = :country')
                        ->setParameter('country', $country)
                        ->groupBy('c.state')
                        ->orderBy('c.state', 'ASC')
                        ->getQuery()
                        ->getResult()
        ;
    }

    /**
     * 
     * @param string $state
     * @return City[]
     */
    public function findByState(string $state)
    {
        return $this->createQueryBuilder('c')
                        ->where('c.state = :state')
                        ->setParameter('state', $state)
                        ->orderBy('c.name', 'ASC')
                        ->getQuery()
                        ->getResult()
        ;
    }

//    /**
//     * @return City[] Returns an array of City objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }
//    public function findOneBySomeField($value): ?City
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
