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
    * @return Game[] Returns an array of Game objects
    */
    public function findByFirstLetterName($letter, $orderField , $orderValue)
    {
        if( preg_match('/[a-zA-Z]/i', $letter) === 1 ) {
            return $this->createQueryBuilder('g')
                ->andWhere('LOWER(g.name) LIKE :val')
                ->setParameter('val', $letter.'%')
                ->orderBy( 'g.'.$orderField, strtoupper($orderValue) )
                ->getQuery()
                ->getResult()
            ;
        } else {
            /*return $this->createQueryBuilder('g')
                ->andWhere('COALESCE(TRIM(g.name),"") = ""')
                ->orderBy( 'g.'.$orderField, strtoupper($orderValue) )
                ->getQuery()
                ->getResult()
            ;*/
            return [];
        }
    }

    /**
    * @return Array[<int>,<Array>] Returns an array of agregate <game group by first letter> and <games count for this first letter>
    */
    public function findAlphabeticListOfGamesRelatedCounter()
    {

        $connection = $this->getEntityManager()
            ->getConnection();
        $sql = '
            SELECT LOWER(LEFT( g.name , 1 )) AS first_letter , 
                SUM( 1 ) AS games_count 
            FROM game g
            GROUP BY first_letter 
            ORDER BY first_letter ASC
            ';
        $stmt = $connection->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();

    }

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
