<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Trick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Trick|null find($id, $lockMode = null, $lockVersion = null)
 * @method Trick|null findOneBy(array $criteria, array $orderBy = null)
 * @method Trick[]    findAll()
 * @method Trick[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrickRepository extends ServiceEntityRepository
{
    /**
     * @var Paginator
     */
    private $paginator;
    /**
     * @var ManagerRegistry
     */
    private $registry;

    /**
     * TrickRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trick::class);
        $this->registry = $registry;
    }

    /**
     * @return array
     */
    public function findAllVisible(): array
    {
        return $this->findVisibleQuery()
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int|null $categoryID
     * @return int
     */
    public function howManyTricks(int $categoryID = null): int
    {
        $query = $this->findVisibleQuery();
        if ($categoryID != null) {
            $query
                ->leftJoin("p.categories", "c")
                ->where("c.id = " . $categoryID);
        }

        $paginator = new Paginator($query);
        return count($paginator);
    }

    /**
     * @param int $page
     * @param int $pageSize
     * @param int|null $categoryID
     * @return array
     */
    public function findVisibleByPage(int $page, int $pageSize, int $categoryID = null): array
    {
        $query = $this->findVisibleQuery();

        if ($categoryID) {
            $query
                ->leftJoin("p.categories", "c")
                ->where("c.id = " . $categoryID);
        }
        return $query
            ->setFirstResult($pageSize * ($page - 1))
            ->setMaxResults($pageSize)
            ->getQuery()
            ->getResult();
    }


    /**
     * @param int $number Number of tricks to return
     * @return Trick[]
     */
    public function findVisibleLatest(int $number): array
    {
        return $this->findVisibleQuery()
            ->setMaxResults($number)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return QueryBuilder
     */
    private function findVisibleQuery(): QueryBuilder
    {
        return $this->createQueryBuilder("p")
            ->orderBy("p.date_add", "desc")
            ->where("p.visible = true");
    }
}
