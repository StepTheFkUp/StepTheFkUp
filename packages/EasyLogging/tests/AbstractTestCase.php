<?php

declare(strict_types=1);

namespace EonX\EasyLogging\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

/**
 * This class has for objective to provide common features to all tests without having to update
 * the class they all extend.
 */
abstract class AbstractTestCase extends TestCase
{
    /**
     * Calls object's private method and returns its result.
     *
     * @param object $object
     * @param string $method
     * @param mixed ...$args
     *
     * @return mixed
     */
    protected function callPrivateMethod($object, string $method, ...$args)
    {
        return (function ($method, $args) {
            return $this->{$method}(...$args);
        })->call($object, $method, $args);
    }

    /**
     * Returns object's private property value.
     *
     * @param object $object
     * @param string $property
     *
     * @return mixed
     */
    protected function getPrivatePropertyValue($object, string $property)
    {
        return (function ($property) {
            return $this->{$property};
        })->call($object, $property);
    }

    /**
     * Sets private property value.
     *
     * @param object $object
     * @param string $property
     * @param mixed $value
     */
    protected function setPrivatePropertyValue($object, string $property, $value): void
    {
        (function ($property, $value): void {
            $this->{$property} = $value;
        })->call($object, $property, $value);
    }

    protected function tearDown(): void
    {
        $fs = new Filesystem();
        $var = __DIR__ . '/../var';

        if ($fs->exists($var)) {
            $fs->remove($var);
        }

        parent::tearDown();
    }
}
