<?php

namespace App\Repository;

use App\Entity\Trick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @method Trick|null find($id, $lockMode = null, $lockVersion = null)
 * @method Trick|null findOneBy(array $criteria, array $orderBy = null)
 * @method Trick[]    findAll()
 * @method Trick[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrickRepository extends ServiceEntityRepository
{
    /**
     * TrickRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trick::class);
    }

    /**
     * @return Trick[]
     */
    public function findAllVisible(): array
    {
        return $this->findVisibleQuery()
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $page
     * @param $numberPerPage
     * @return Paginator
     */
    public function findAllVisiblePaginate($page, $numberPerPage): Paginator
    {
        if (!is_numeric($page)) {
            throw new InvalidArgumentException(
                'La valeur de l\'argument $page est incorrecte (valeur : ' . $page . ').'
            );
        }

        if ($page < 1) {
            throw new NotFoundHttpException('La page demandée n\'existe pas');
        }

        if (!is_numeric($numberPerPage)) {
            throw new InvalidArgumentException(
                'La valeur de l\'argument $numberPerPage est incorrecte (valeur : ' . $numberPerPage . ').'
            );
        }

        $queryBuilder = $this->createQueryBuilder('p')
            ->where('CURRENT_DATE() >= p.date_add')
            ->orderBy('p.date_add', 'DESC');

        $query = $queryBuilder->getQuery();

        $firstResult = ($page - 1) * $numberPerPage;
        $query->setFirstResult($firstResult)->setMaxResults($numberPerPage);
        $paginator = new Paginator($query);

        if (($paginator->count() <= $firstResult) && $page != 1) {
            throw new NotFoundHttpException('La page demandée n\'existe pas.'); // page 404, sauf pour la première page
        }

        return $paginator;
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
            ->where("p.visible = true");
    }


}
