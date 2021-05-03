<?php

declare(strict_types=1);

namespace EonX\EasyCore\Doctrine\Dispatchers;

use EonX\EasyCore\Interfaces\DatabaseEntityInterface;
use EonX\EasyCore\Doctrine\Events\EntityCreatedEvent;
use EonX\EasyCore\Doctrine\Events\EntityUpdatedEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class DeferredEntityEventDispatcher implements DeferredEntityEventDispatcherInterface
{
    /**
     * @var bool
     */
    private $enabled;

    /**
     * @var \EonX\EasyCore\Interfaces\DatabaseEntityInterface[]
     */
    private $entityInsertions = [];

    /**
     * @var \EonX\EasyCore\Interfaces\DatabaseEntityInterface[]
     */
    private $entityUpdates = [];

    /**
     * @var \Symfony\Contracts\EventDispatcher\EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->enabled = true;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function clear(?int $transactionNestingLevel = null): void
    {
        if ($transactionNestingLevel !== null) {
            foreach ($this->entityInsertions as $level => $entities) {
                if ($level >= $transactionNestingLevel) {
                    $this->entityInsertions[$level] = [];
                }
            }

            foreach ($this->entityUpdates as $level => $entities) {
                if ($level >= $transactionNestingLevel) {
                    $this->entityUpdates[$level] = [];
                }
            }

            return;
        }

        $this->entityInsertions = [];
        $this->entityUpdates = [];
    }

    public function deferInsertions(array $entityInsertions, int $transactionNestingLevel): void
    {
        if ($this->enabled === false) {
            return;
        }

        /** @var \EonX\EasyCore\Interfaces\DatabaseEntityInterface[] $mergedEntityInsertions */
        $mergedEntityInsertions = \array_merge(
            (array)($this->entityInsertions[$transactionNestingLevel] ?? []),
            $entityInsertions
        );
        $this->entityInsertions[$transactionNestingLevel] = $mergedEntityInsertions;
    }

    public function deferUpdates(array $entityUpdates, int $transactionNestingLevel): void
    {
        if ($this->enabled === false) {
            return;
        }

        /** @var \EonX\EasyCore\Interfaces\DatabaseEntityInterface[] $mergedEntityUpdates */
        $mergedEntityUpdates = \array_merge(
            (array)($this->entityUpdates[$transactionNestingLevel] ?? []),
            $entityUpdates
        );
        $this->entityUpdates[$transactionNestingLevel] = $mergedEntityUpdates;
    }

    public function disable(): void
    {
        $this->enabled = false;
    }

    public function dispatch(): void
    {
        $entityInsertions = $this->entityInsertions;
        $entityUpdates = $this->entityUpdates;

        $this->clear();

        if ($this->enabled === false) {
            return;
        }

        \array_walk_recursive($entityInsertions, function (DatabaseEntityInterface $entity) {
            $this->eventDispatcher->dispatch(new EntityCreatedEvent($entity), EntityCreatedEvent::NAME);
        });

        \array_walk_recursive($entityUpdates, function (DatabaseEntityInterface $entity) {
            $this->eventDispatcher->dispatch(new EntityUpdatedEvent($entity), EntityUpdatedEvent::NAME);
        });
    }

    public function enable(): void
    {
        $this->enabled = true;
    }
}
