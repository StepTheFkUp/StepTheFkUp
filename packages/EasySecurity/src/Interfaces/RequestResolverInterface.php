<?php

declare(strict_types=1);

namespace EonX\EasySecurity\Interfaces;

use Symfony\Component\HttpFoundation\Request;

interface RequestResolverInterface
{
    public function getRequest(): Request;
}
