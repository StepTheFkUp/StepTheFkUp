<?php
declare(strict_types=1);

namespace EonX\EasyPipeline\Tests;

use Mockery;
use Mockery\LegacyMockInterface;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;

/**
 * This class has for objective to provide common features to all tests without having to update
 * the class they all extend.
 */
abstract class AbstractTestCase extends TestCase
{
    /**
     * Convert protected/private method to public.
     *
     * @param string $className
     * @param string $methodName
     *
     * @return \ReflectionMethod
     *
     * @throws \ReflectionException
     */
    protected function getMethodAsPublic(string $className, string $methodName): ReflectionMethod
    {
        $class = new ReflectionClass($className);
        $method = $class->getMethod($methodName);
        $method->setAccessible(true);

        return $method;
    }

    /**
     * Create mock for given class and apply expectations if given.
     *
     * @param string|object $class
     * @param null|callable $expectations
     *
     * @return \Mockery\LegacyMockInterface
     */
    protected function mock($class, ?callable $expectations = null): LegacyMockInterface
    {
        $mock = Mockery::mock($class);

        if ($expectations !== null) {
            $expectations($mock);
        }

        return $mock;
    }
}


