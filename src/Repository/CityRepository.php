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

use App\Entity\City;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Contracts\Cache\CacheInterface;

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

    private KernelInterface $kernel;
    
    public function __construct(
            ManagerRegistry $registry,
//            KernelInterface $kernel,
    ) {
        parent::__construct($registry, City::class);
        
//        $this->kernel = $kernel;
    }

    public function save(City $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function select(int $id): City
    {
        /** @var City $citySelected */
        $citySelected = $this->createQueryBuilder('c')
                ->where('c.selected = 1')
                ->getQuery()
                ->getOneOrNullResult()
        ;

        if (null != $citySelected) {
            $citySelected->setSelected(false);
            $this->save($citySelected);
        }

        $city = $this->find($id);
        $city->setSelected(true);
        $this->save($city, true);
        
//        $application = new Application($this->kernel);
//        $application->setAutoExit(false);
//        
//        $input = new ArrayInput([
//            'command' => 'cache:pool:clear',
//            'pools' => ['cache.doctrine.orm.default.result'],
//        ]);
//        
//        $application->run($input);

        return $city;
    }

    /**
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
                        ->enableResultCache(3600)
                        ->getSingleColumnResult()
        ;
    }

    /**
     * @return City[]
     */
    public function listCityFromCountryAndState(string $country, string $state, string $name = null, int $firstResult = 1, int $maxResults = 10)
    {
        $query = $this->createQueryBuilder('c')
                ->where('c.state = :state')
                ->setParameter('state', $state)
        ;

        if (null != $name) {
            $query->andWhere('c.name like :name')
                    ->setParameter('name', '%' . $name . '%')
            ;
        }

        $query->orWhere('c.selected = true')
                ->setMaxResults($maxResults)
                ->setFirstResult($firstResult)
                ->orderBy('c.name', 'ASC')
                ->getQuery()
                ->enableResultCache(3600);
        ;

        return new Paginator($query);
    }

    /**
     * @return City[]
     */
    public function listCitySelected()
    {
        return $this->createQueryBuilder('c')
                        ->where('c.selected = 1')
                        ->getQuery()
                        ->getResult()
        ;
    }

    /**
     * @param string $state
     *
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
                        ->enableResultCache(3600)
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
