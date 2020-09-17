<?php

namespace App\Repository;

use App\Entity\ChatPost;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ChatPost|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChatPost|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChatPost[]    findAll()
 * @method ChatPost[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChatPostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChatPost::class);
    }

    // /**
    //  * @return ChatPost[] Returns an array of ChatPost objects
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
    public function findOneBySomeField($value): ?ChatPost
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
