<?php
declare(strict_types=1);

namespace StepTheFkUp\EasyRepository\Implementations\Doctrine\ORM;

use Doctrine\Common\Persistence\ManagerRegistry;
use StepTheFkUp\EasyRepository\Interfaces\ObjectRepositoryInterface;

abstract class AbstractDoctrineOrmRepository implements ObjectRepositoryInterface
{
    use DoctrineOrmRepositoryTrait;

    /**
     * AbstractDoctrineOrmRepository constructor.
     *
     * @param \Doctrine\Common\Persistence\ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        $entityClass = $this->getEntityClass();

        $this->manager = $registry->getManagerForClass($entityClass);
        $this->repository = $this->manager->getRepository($entityClass);
    }
}
