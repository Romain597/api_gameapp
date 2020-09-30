<?php

namespace App\Repository;

use App\Entity\Game;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Game|null find($id, $lockMode = null, $lockVersion = null)
 * @method Game|null findOneBy(array $criteria, array $orderBy = null)
 * @method Game[]    findAll()
 * @method Game[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

    /**
     * @return int Return the number of Game objects
    */
    public function countAllGame()
    {
        return $this->createQueryBuilder('g')
            ->select('COUNT(g.id) AS nbRow')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    /**
     * @return Game[] Returns an array of Game objects by the select category
    */
    /*public function findByCategoryId( int $categoryId , $orderBy = [], $limit = null, $offset = null )
    {
        return $this->createQueryBuilder('g')
            ->innerJoin('a.categories', 'c')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.datePublication', 'DESC')
            ->setFirstResult(0)
            ->setMaxResults(5)
            ->getQuery()
            ->getResult()
        ;
    }*/

    // /**
    //  * @return Game[] Returns an array of Game objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Game
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
