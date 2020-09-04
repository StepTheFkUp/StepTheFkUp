<?php

declare(strict_types=1);

namespace EonX\EasyStandard\Tests\Sniffs\Namespaces;

use EonX\EasyStandard\Sniffs\Namespaces\Psr4Sniff;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class Psr4SniffTest extends AbstractCheckerTestCase
{
    /**
     * @return iterable<SmartFileInfo|int>
     */
    public function providerTestSniff(): iterable
    {
        yield [new SmartFileInfo(__DIR__ . '/../../fixtures/Sniffs/Namespaces/Psr4SniffTest.php.inc'), 1];
    }

    /**
     * @dataProvider providerTestSniff()
     */
    public function testSniff(SmartFileInfo $smartFileInfo, int $expectedErrorCount): void
    {
        $this->doTestFileInfoWithErrorCountOf($smartFileInfo, $expectedErrorCount);
    }

    protected function getCheckerClass(): string
    {
        return Psr4Sniff::class;
    }
}
