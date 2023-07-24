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
     * @return array<int,string>
     */
    public function listCountry()
    {
        return $this->createQueryBuilder('c')
                        ->select('c.country')
                        ->groupBy('c.country')
                        ->orderBy('c.country', 'ASC')
                        ->getQuery()
                        ->enableResultCache(300)
                        ->getSingleColumnResult()
        ;
    }

    /**
     * 
     * @param string $country
     * @return array<int,string>
     */
    public function listStateFromCountry(string $country)
    {
        return $this->createQueryBuilder('c')
                        ->select('c.state')
                        ->where('c.country = :country')
                        ->setParameter('country', $country)
                        ->groupBy('c.state')
                        ->orderBy('c.state', 'ASC')
                        ->getQuery()
                        ->enableResultCache(300)
                        ->getSingleColumnResult()
        ;
    }

    /**
     * 
     * @param string $state
     * @return City[]
     */
    public function listCityFromState(string $state)
    {
        return $this->createQueryBuilder('c')
                        ->where('c.state = :state')
                        ->setParameter('state', $state)
                        ->orderBy('c.name', 'ASC')
                        ->getQuery()
                        ->enableResultCache(300)
                        ->getResult()
        ;
    }

    /**
     * 
     * @param string $state
     * @return City[]
     */
    public function findByCountryStateIdCity(?string $country, ?string $state, ?string $idCity)
    {
        return $this->createQueryBuilder('c')
                        ->where('c.country = :country')
                        ->setParameter('country', $country)
                        ->andWhere('c.state = :state')
                        ->setParameter('state', $state)
                        ->andWhere('c.id = :idCity')
                        ->setParameter('idCity', $idCity)
                        ->getQuery()
                        ->getOneOrNullResult()
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
