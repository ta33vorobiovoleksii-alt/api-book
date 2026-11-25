<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    /**
     * Пошук за назвою/автором та фільтрація за ID автора через QueryBuilder
     * @return Book[]
     */
    public function findBySearchAndAuthor(?string $query, ?int $authorId): array
    {
        $qb = $this->createQueryBuilder('b')
            ->leftJoin('b.author', 'a')
            ->addSelect('a')
            ->orderBy('b.id', 'DESC');

        if ($query !== null && trim($query) !== '') {
            $qb->andWhere('LOWER(b.title) LIKE :q OR LOWER(a.name) LIKE :q')
               ->setParameter('q', '%' . mb_strtolower(trim($query)) . '%');
        }

        if ($authorId !== null && $authorId > 0) {
            $qb->andWhere('a.id = :authorId')
               ->setParameter('authorId', $authorId);
        }

        return $qb->getQuery()->getResult();
    }
}