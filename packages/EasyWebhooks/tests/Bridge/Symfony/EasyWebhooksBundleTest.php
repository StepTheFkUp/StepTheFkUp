<?php

declare(strict_types=1);

namespace EonX\EasyWebhooks\Tests\Bridge\Symfony;

use EonX\EasyWebhooks\Bridge\BridgeConstantsInterface;
use EonX\EasyWebhooks\Configurators\BodyFormatterWebhookConfigurator;
use EonX\EasyWebhooks\Configurators\MethodWebhookConfigurator;
use EonX\EasyWebhooks\Signers\Rs256Signer;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class EasyWebhooksBundleTest extends AbstractSymfonyTestCase
{
    /**
     * @return iterable<mixed>
     */
    public function providerTestConfigAndDependenciesSanity(): iterable
    {
        yield 'Defaults' => [
            [],
            static function (ContainerInterface $container): void {
                self::assertFalse($container->hasParameter(BridgeConstantsInterface::PARAM_SECRET));
                self::assertFalse($container->hasParameter(BridgeConstantsInterface::PARAM_SIGNATURE_HEADER));
                self::assertFalse($container->has(BridgeConstantsInterface::SIGNER));
                self::assertInstanceOf(
                    BodyFormatterWebhookConfigurator::class,
                    $container->get(BodyFormatterWebhookConfigurator::class)
                );
                self::assertInstanceOf(
                    MethodWebhookConfigurator::class,
                    $container->get(MethodWebhookConfigurator::class)
                );
            },
        ];

        yield 'Signature Defaults' => [
            [__DIR__ . '/Fixtures/config/signature_defaults.yaml'],
            static function (ContainerInterface $container): void {
                self::assertNull($container->getParameter(BridgeConstantsInterface::PARAM_SECRET));
                self::assertNull($container->getParameter(BridgeConstantsInterface::PARAM_SIGNATURE_HEADER));
                self::assertInstanceOf(Rs256Signer::class, $container->get(BridgeConstantsInterface::SIGNER));
            },
        ];

        yield 'Signature Custom' => [
            [__DIR__ . '/Fixtures/config/signature_custom.yaml'],
            static function (ContainerInterface $container): void {
                self::assertEquals('my-secret', $container->getParameter(BridgeConstantsInterface::PARAM_SECRET));
                self::assertEquals(
                    'X-My-Header',
                    $container->getParameter(BridgeConstantsInterface::PARAM_SIGNATURE_HEADER)
                );
            },
        ];

        yield 'No default configurators' => [
            [__DIR__ . '/Fixtures/config/no_default_configurators.yaml'],
            static function (ContainerInterface $container): void {
                self::assertFalse($container->has(BodyFormatterWebhookConfigurator::class));
                self::assertFalse($container->has(MethodWebhookConfigurator::class));
            },
        ];
    }

    /**
     * @param string[] $configs
     *
     * @dataProvider providerTestConfigAndDependenciesSanity
     */
    public function testConfigAndDependenciesSanity(array $configs, callable $tests): void
    {
        $tests($this->getKernel($configs)->getContainer());
    }
}
