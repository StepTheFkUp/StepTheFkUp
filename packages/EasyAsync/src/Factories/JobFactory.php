<?php

declare(strict_types=1);

namespace EonX\EasyAsync\Factories;

use EonX\EasyAsync\Data\Job;
use EonX\EasyAsync\Interfaces\JobFactoryInterface;
use EonX\EasyAsync\Interfaces\JobInterface;
use EonX\EasyAsync\Interfaces\TargetInterface;

/**
 * @deprecated since 3.0.0, will be removed in 3.1. Use Batch features instead.
 */
final class JobFactory implements JobFactoryInterface
{
    public function create(TargetInterface $target, string $type, ?int $total = null): JobInterface
    {
        return new Job($target, $type, $total);
    }
}
