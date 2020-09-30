<?php

namespace App\Repository;

use App\Entity\Studio;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Studio|null find($id, $lockMode = null, $lockVersion = null)
 * @method Studio|null findOneBy(array $criteria, array $orderBy = null)
 * @method Studio[]    findAll()
 * @method Studio[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StudioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Studio::class);
    }

    /**
    * @return Studio[] Returns an array of Studio objects
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
    * @return Array[<int>,<Array>] Returns an array of agregate <studio group by first letter> and <games count for this first letter>
    */
    public function findAlphabeticListOfStudiosWithGamesRelatedCounter()
    {

        $connection = $this->getEntityManager()
            ->getConnection();
        $sql = '
            SELECT LOWER(LEFT( s.name , 1 )) AS first_letter , 
                SUM( (SELECT COUNT(j.game_id) FROM game_studio j WHERE j.studio_id = s.id) ) AS games_count 
            FROM studio s 
            GROUP BY first_letter 
            ORDER BY first_letter ASC
            ';
        $stmt = $connection->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();

    }

    // /**
    //  * @return Studio[] Returns an array of Studio objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Studio
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
