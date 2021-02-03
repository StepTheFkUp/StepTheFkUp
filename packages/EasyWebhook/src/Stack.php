<?php

declare(strict_types=1);

namespace EonX\EasyWebhook;

use EonX\EasyUtils\CollectorHelper;
use EonX\EasyWebhook\Exceptions\NoNextMiddlewareException;
use EonX\EasyWebhook\Interfaces\MiddlewareInterface;
use EonX\EasyWebhook\Interfaces\StackInterface;

final class Stack implements StackInterface
{
    /**
     * @var int
     */
    private $index = 0;

    /**
     * @var \EonX\EasyWebhook\Interfaces\MiddlewareInterface[]
     */
    private $middleware;

    /**
     * @param iterable<mixed> $middleware
     * @param iterable<mixed> $coreMiddleware
     */
    public function __construct(iterable $middleware, iterable $coreMiddleware)
    {
        $this->middleware = CollectorHelper::orderLowerPriorityFirstAsArray(
            CollectorHelper::filterByClass($middleware, MiddlewareInterface::class)
        );

        $coreMiddleware = CollectorHelper::orderLowerPriorityFirst(
            CollectorHelper::filterByClass($coreMiddleware, MiddlewareInterface::class)
        );

        // Add core middleware after the other middleware
        foreach ($coreMiddleware as $middleware) {
            $this->middleware[] = $middleware;
        }
    }

    public function next(): MiddlewareInterface
    {
        $next = $this->middleware[$this->index] ?? null;

        // This shouldn't happen as we must make sure SendWebhookMiddleware is always the last one
        if ($next === null) {
            throw new NoNextMiddlewareException(\sprintf('No next middleware for index %d', $this->index));
        }

        ++$this->index;

        return $next;
    }
}
