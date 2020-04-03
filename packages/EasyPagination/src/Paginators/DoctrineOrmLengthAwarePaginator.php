<?php

declare(strict_types=1);

namespace EonX\EasyPagination\Paginators;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EonX\EasyPagination\Interfaces\StartSizeDataInterface;
use EonX\EasyPagination\Traits\DoctrinePaginatorTrait;

final class DoctrineOrmLengthAwarePaginator extends AbstractTransformableLengthAwarePaginator
{
    use DoctrinePaginatorTrait;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $manager;

    public function __construct(
        EntityManagerInterface $manager,
        StartSizeDataInterface $startSizeData,
        string $from,
        string $fromAlias,
        ?string $path = null
    ) {
        $this->from = $from;
        $this->fromAlias = $fromAlias;
        $this->manager = $manager;

        parent::__construct($startSizeData, $path);
    }

    protected function doCreateQueryBuilder(): QueryBuilder
    {
        return $this->manager->createQueryBuilder();
    }

    /**
     * @return mixed[]
     */
    protected function doGetResult(QueryBuilder $queryBuilder): array
    {
        return $queryBuilder->getQuery()->getResult();
    }

    protected function doGetTotalItems(QueryBuilder $queryBuilder, string $countAlias): int
    {
        return (int)($queryBuilder->getQuery()->getResult()[0][$countAlias] ?? 0);
    }
}
