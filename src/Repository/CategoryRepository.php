<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
    * @return Category[] Returns an array of Category objects
    */
    public function findByFirstLetterName($letter, $orderField , $orderValue)
    {
        if( preg_match('/[a-zA-Z]/i', $letter) === 1 ) {
            return $this->createQueryBuilder('s')
                ->andWhere('LOWER(s.name) LIKE :val')
                ->setParameter('val', $letter.'%')
                ->orderBy( 's.'.$orderField, strtoupper($orderValue) )
                ->getQuery()
                ->getResult()
            ;
        } else {
            return $this->createQueryBuilder('s')
                ->andWhere('COALESCE(TRIM(s.name),"") = ""')
                ->orderBy( 's.'.$orderField, strtoupper($orderValue) )
                ->getQuery()
                ->getResult()
            ;
        }
    }

    /**
    * @return Array[<int>,<Array>] Returns an array of agregate <category group by first letter> and <games count for this first letter>
    */
    public function findAlphabeticListOfCategoriesWithGamesRelatedCounter()
    {

        $connection = $this->getEntityManager()
            ->getConnection();
        $sql = '
            SELECT LOWER(LEFT( c.name , 1 )) AS first_letter , 
                SUM( (SELECT COUNT(j.game_id) FROM game_category j WHERE j.category_id = c.id) ) AS games_count 
            FROM category c 
            GROUP BY first_letter 
            ORDER BY first_letter ASC
            ';
        $stmt = $connection->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();

    }

    // /**
    //  * @return Category[] Returns an array of Category objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Category
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
