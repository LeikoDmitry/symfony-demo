<?php

namespace SymfonyDemo\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use DateTime;
use Exception;
use Laminas\Paginator\Paginator;
use SymfonyDemo\Entity\Post;
use SymfonyDemo\Entity\Tag;

class PostRepository extends EntityRepository
{
    /**
     * @param  int  $page
     * @param  Tag $tag
     *
     * @return Paginator
     * @throws Exception
     */
    public function findLatest(int $page = 1, Tag $tag = null): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->addSelect('a', 't')
            ->innerJoin('p.author', 'a')
            ->leftJoin('p.tags', 't')
            ->where('p.publishedAt <= :now')
            ->orderBy('p.publishedAt', 'DESC')
            ->setParameter('now', new DateTime())
        ;
        if (null !== $tag) {
            $queryBuilder->andWhere(':tag MEMBER OF p.tags')
                ->setParameter('tag', $tag);
        }
        $adapter = new DoctrineAdapter(new ORMPaginator($queryBuilder));

        return (new Paginator($adapter))->setCurrentPageNumber($page);
    }

    /**
     * @param  string  $query
     * @param  int  $limit
     *
     * @return array
     */
    public function findBySearchQuery(string $query, int $limit = Post::NUM_ITEMS): array
    {
        $searchTerms = $this->extractSearchTerms($query);

        if (0 === \count($searchTerms)) {
            return [];
        }
        $queryBuilder = $this->createQueryBuilder('p');
        foreach ($searchTerms as $key => $term) {
            $queryBuilder
                ->orWhere('p.title LIKE :t_' . $key)
                ->setParameter('t_' . $key, '%' . $term . '%');
        }

        return $queryBuilder
            ->orderBy('p.publishedAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param  string  $searchQuery
     *
     * @return array
     */
    private function extractSearchTerms(string $searchQuery): array
    {
        $searchQuery = preg_replace('/[[:space:]]+/', ' ', $searchQuery);
        $terms = array_unique(explode(' ', $searchQuery));

        return array_filter($terms, function ($term) {
            return 2 <= mb_strlen($term);
        });
    }
}
