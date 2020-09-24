<?php

namespace App\Repository;

use App\Entity\ChatPost;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
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

    /**
     * @param int $trickID
     * @return int
     */
    public function howManyPosts(int $trickID): int
    {
        $query = $this->createQueryBuilder("p");
        if ($trickID != null) {
            $query
                ->where("p.trick = " . $trickID);
        }

        $paginator = new Paginator($query);
        return count($paginator);
    }

    public function findByPage(int $page, int $pageSize, int $trickID): array
    {
        $query = $this->createQueryBuilder("p");
        $nbrOfPosts = $this->howManyPosts($trickID);
        $offset = $nbrOfPosts - ($pageSize * $page);
        if ($offset < 0) {
            $offset = 0;
        }

        return $query
            ->setFirstResult($offset)
            ->setMaxResults($pageSize)
            ->orderBy("p.date_add", "asc")
            ->where("p.trick = " . $trickID)
            ->getQuery()
            ->getResult();
    }
}
