<?php

declare(strict_types=1);

use EonX\EasyRequestId\Bridge\EasyLogging\RequestIdProcessor;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->set(RequestIdProcessor::class);
};
