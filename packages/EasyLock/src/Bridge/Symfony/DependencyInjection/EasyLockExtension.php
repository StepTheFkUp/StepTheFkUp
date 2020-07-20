<?php

declare(strict_types=1);

namespace EonX\EasyLock\Bridge\Symfony\DependencyInjection;

use EonX\EasyLock\Bridge\BridgeConstantsInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\Messenger\DependencyInjection\MessengerPass;

final class EasyLockExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.php');

        $container->setAlias(BridgeConstantsInterface::SERVICE_CONNECTION, $config['connection']);

        if (\class_exists(MessengerPass::class)) {
            $loader->load('messenger_middleware.php');
        }
    }
}
