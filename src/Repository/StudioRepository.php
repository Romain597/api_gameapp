<?php

namespace App\Repository;

use App\Entity\Studio;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

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
    public function findAlphabeticListOfStudioWithGamesRelatedCounter()
    {

        return $this->createQuery('SELECT LOWER(LEFT( s.name , 1 )) AS first_letter , SUM( (SELECT COUNT(j.game_id) FROM game_studio j WHERE j.studio_id = s.id) ) AS games_count FROM studio s GROUP BY first_letter ORDER BY first_letter ASC')
            ->getArrayResult();

    }

    /**
    * @return Array[<int>,<Array>] Returns an array of Studio rows
    */
    public function findAllWithGamesRelatedCounter($orderField , $orderValue)
    {

        /*return $this->createQueryBuilder('s')
            ->select(' s , ( SELECT COUNT(j.game_id) FROM game_studio j WHERE j.studio_id = s.id ) AS games_count ')
            ->orderBy( 's.'.$orderField, strtoupper($orderValue) )
            ->getQuery()
            ->getArrayResult()
        ;*/

        /*$em->createQuery('SELECT s.* , ( SELECT COUNT(j.game_id) FROM game_studio j WHERE j.studio_id = s.id ) AS games_count , LOWER(LEFT( s.name , 1 )) AS first_letter FROM studio s')
            ->getArrayResult();*/

            //'SELECT LOWER(LEFT( s.name , 1 )) AS first_letter , SUM( (SELECT COUNT(j.game_id) FROM game_studio j WHERE j.studio_id = s.id) ) AS games_count FROM studio s GROUP BY first_letter ORDER BY first_letter ASC'

        /*// la table en base de données correspondant à l'entité liée au repository en cours
        $table = $this->getClassMetadata()->table["name"];

        // Dans mon cas je voulais trier mes résultats avec un ordre bien particulier
        $sql =  "SELECT m.* "
                ."FROM ".$table." AS m "
                ."WHERE (m.deleted_at IS NULL OR m.deleted_at > :current_time) "
                ."ORDER BY m.status = :status_available DESC, m.status = :status_unknown DESC, m.status = :status_unavailable DESC, m.priority ASC";

        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addEntityResult(MyClass::class, "m");

        // On mappe le nom de chaque colonne en base de données sur les attributs de nos entités
        foreach ($this->getClassMetadata()->fieldMappings as $obj) {
            $rsm->addFieldResult("m", $obj["columnName"], $obj["fieldName"]);
        }

        $stmt = $this->getEntityManager()->createNativeQuery($sql, $rsm);

        $stmt->setParameter(":current_time", new \DateTime("now"));
        $stmt->setParameter(":status_available", MyClass::STATUS_AVAILABLE);
        $stmt->setParameter(":status_unknown", MyClass::STATUS_UNKNOWN);
        $stmt->setParameter(":status_unavailable", MyClass::STATUS_UNAVAILABLE);

        $stmt->execute();

        return $stmt->getArrayResult();*/
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
