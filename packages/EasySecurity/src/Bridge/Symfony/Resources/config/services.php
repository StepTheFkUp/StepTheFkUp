<?php

declare(strict_types=1);

use EonX\EasySecurity\Authorization\AuthorizationMatrixFactory;
use EonX\EasySecurity\Bridge\BridgeConstantsInterface;
use EonX\EasySecurity\Bridge\Symfony\DataCollector\SecurityContextDataCollector;
use EonX\EasySecurity\Bridge\Symfony\Factories\AuthenticationFailureResponseFactory;
use EonX\EasySecurity\Bridge\Symfony\Interfaces\AuthenticationFailureResponseFactoryInterface;
use EonX\EasySecurity\Bridge\Symfony\Security\ContextAuthenticator;
use EonX\EasySecurity\Interfaces\Authorization\AuthorizationMatrixFactoryInterface;
use EonX\EasySecurity\Interfaces\Authorization\AuthorizationMatrixInterface;
use EonX\EasySecurity\MainSecurityContextConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services
        ->defaults()
        ->autowire()
        ->autoconfigure();

    // Authorization
    $services
        ->set(AuthorizationMatrixFactoryInterface::class, AuthorizationMatrixFactory::class)
        ->arg('$rolesProviders', tagged_iterator(BridgeConstantsInterface::TAG_ROLES_PROVIDER))
        ->arg('$permissionsProviders', tagged_iterator(BridgeConstantsInterface::TAG_PERMISSIONS_PROVIDER));

    $services
        ->set(AuthorizationMatrixInterface::class)
        ->factory([ref(AuthorizationMatrixFactoryInterface::class), 'create']);

    // MainSecurityContextConfigurator
    $services
        ->set(MainSecurityContextConfigurator::class)
        ->call('withConfigurators', [tagged_iterator(BridgeConstantsInterface::TAG_CONTEXT_CONFIGURATOR)])
        ->call('withModifiers', [tagged_iterator(BridgeConstantsInterface::TAG_CONTEXT_MODIFIER)]);

    // Symfony Security
    $services->set(ContextAuthenticator::class);
    $services->set(AuthenticationFailureResponseFactoryInterface::class, AuthenticationFailureResponseFactory::class);

    // DataCollector
    $services
        ->set(SecurityContextDataCollector::class)
        ->tag('data_collector', [
            'template' => '@EasySecurity/Collector/security_context_collector.html.twig',
            'id' => 'easy_security.security_context_collector',
        ]);
};
