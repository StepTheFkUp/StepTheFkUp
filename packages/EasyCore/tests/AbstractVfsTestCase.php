<?php
declare(strict_types=1);

namespace EonX\EasyCore\Tests;

use org\bovigo\vfs\vfsStreamWrapper;

/**
 * Registers vfs: stream protocol.
 */
abstract class AbstractVfsTestCase extends AbstractTestCase
{
    /**
     * {@inheritdoc}
     *
     * @throws \org\bovigo\vfs\vfsStreamException
     */
    public function setUp(): void
    {
        vfsStreamWrapper::register();

        parent::setUp();
    }
}
