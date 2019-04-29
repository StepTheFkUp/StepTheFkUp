<?php
declare(strict_types=1);

namespace LoyaltyCorp\EasyApiToken\Tests\Factories;

use LoyaltyCorp\EasyApiToken\Decoders\BasicAuthDecoder;
use LoyaltyCorp\EasyApiToken\Exceptions\InvalidConfigurationException;
use LoyaltyCorp\EasyApiToken\Factories\EasyApiDecoderFactory;
use LoyaltyCorp\EasyApiToken\Tests\AbstractTestCase;

/**
 * @covers \LoyaltyCorp\EasyApiToken\Factories\EasyApiDecoderFactory
 */
final class EasyApiDecoderFactoryTest extends AbstractTestCase
{
    /**
     * Test that an empty exception throws an error.
     *
     * @throws \LoyaltyCorp\EasyApiToken\Exceptions\InvalidConfigurationException
     */
    public function testNullCreation(): void
    {
        $factory = new EasyApiDecoderFactory([]);

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Could not find a valid configuration.');

        $factory->build();
    }

    /**
     * Test that a basic driver is configured on request.
     *
     * @throws \LoyaltyCorp\EasyApiToken\Exceptions\InvalidConfigurationException
     */
    public function testBasicAuthCreation(): void
    {
        $factory = new EasyApiDecoderFactory(['something' => ['driver' => 'basic']]);

        $actual = $factory->build('something');

        $this->assertInstanceOf(BasicAuthDecoder::class, $actual);
    }

    /**
     * Test that a chain driver is configured on request.
     *
     * @throws \LoyaltyCorp\EasyApiToken\Exceptions\InvalidConfigurationException
     */
    public function test(): void
    {
        $factory = new EasyApiDecoderFactory(['onething' => ['driver' => 'basic']]);

        $factory->build('some_other_thing');

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Could not find EasyApiConfiguration for key: some_other_thing.');
    }
}

\class_alias(
    EasyApiDecoderFactoryTest::class,
    'StepTheFkUp\EasyApiToken\Tests\Factories\EasyApiDecoderFactoryTest',
    false
);
