<?php

namespace App\Repository;

use App\Entity\EmbedVideo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EmbedVideo|null find($id, $lockMode = null, $lockVersion = null)
 * @method EmbedVideo|null findOneBy(array $criteria, array $orderBy = null)
 * @method EmbedVideo[]    findAll()
 * @method EmbedVideo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmbedVideoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmbedVideo::class);
    }

    // /**
    //  * @return EmbedVideo[] Returns an array of EmbedVideo objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EmbedVideo
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
