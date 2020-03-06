<?php
declare(strict_types=1);

namespace EonX\EasyCore\Bridge\Symfony\ApiPlatform\Pagination;

use Symfony\Component\HttpKernel\Event\ViewEvent;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Paginator;

final class CustomPaginationListener
{
    public function __invoke(ViewEvent $event): void
    {
        $result = $event->getControllerResult();

        if ($result instanceof Paginator === false) {
            return;
        }

        $event->setControllerResult(new CustomPaginator($result));
    }
}
