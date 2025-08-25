<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

class OrderRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly int $itemsPerPage
    ) {
        parent::__construct($registry, Order::class);
    }

    public function findByNewestPaginated(int $page = 1): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('o')
            ->orderBy('o.createdAt', 'DESC')
            ->setFirstResult($this->itemsPerPage * ($page - 1))
            ->setMaxResults($this->itemsPerPage);

        return new Paginator($queryBuilder->getQuery());
    }
}
