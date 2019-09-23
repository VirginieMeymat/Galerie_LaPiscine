<?php

namespace App\Repository;

use App\Entity\Artist;
use App\Entity\Work;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Artist|null find($id, $lockMode = null, $lockVersion = null)
 * @method Artist|null findOneBy(array $criteria, array $orderBy = null)
 * @method Artist[]    findAll()
 * @method Artist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArtistRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Artist::class);
    }

    public function findByName($name, $category = 'all')
    {
        if($category == 'all'){
            return $this->createQueryBuilder('a')
                ->where('a.name LIKE :name')
                ->setParameter('name', '%'.$name.'%')
                ->orderBy('a.name', 'ASC')
                ->getQuery()
                ->getResult();
        }
        else{
            return $this->createQueryBuilder('a')
                ->where('a.name LIKE :name')
                ->andWhere('a.category = :category')
                ->setParameter('name', '%'.$name.'%')
                ->setParameter('category', $category)
                ->orderBy('a.name', 'ASC')
                ->getQuery()
                ->getResult();
        }

    }

    public function findByCategory($id_category)
    {
        return $this->createQueryBuilder('a')
            ->where('a.category = :category')
            ->setParameter('category', $id_category)
            ->orderBy('a.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
    // /**
    //  * @return Artist[] Returns an array of Artiste objects
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
    public function findOneBySomeField($value): ?Artist
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
