<?php

declare(strict_types=1);

use EonX\EasyErrorHandler\Bridge\BridgeConstantsInterface;
use EonX\EasyErrorHandler\Bridge\Symfony\Builder\ApiPlatformBuilderProvider;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services
        ->set(ApiPlatformBuilderProvider::class)
        ->arg('$keys', '%' . BridgeConstantsInterface::PARAM_RESPONSE_KEYS . '%');

    // Drop tags of ValidationExceptionListener
    $services
        ->set('api_platform.listener.exception.validation')
        ->synthetic(true);
};
