<?php

declare(strict_types=1);

namespace EonX\EasyStandard\Tests\Sniffs\Commenting;

use EonX\EasyStandard\Sniffs\Commenting\AnnotationSortingSniff;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class AnnotationSortingSniffTest extends AbstractCheckerTestCase
{
    /**
     * @return iterable<mixed>
     */
    public function providerTestSniff(): iterable
    {
        yield [__DIR__ . '/../../fixtures/Sniffs/Commenting/AnnotationSortingSniffTest.php.inc'];
    }

    /**
     * @dataProvider providerTestSniff
     */
    public function testSniff(string $file): void
    {
        $this->doTestFileInfo(new SmartFileInfo($file));
    }

    protected function getCheckerClass(): string
    {
        return AnnotationSortingSniff::class;
    }

    /**
     * @return mixed[]
     */
    protected function getCheckerConfiguration(): array
    {
        return [
            'alwaysTopAnnotations' => ['@param', '@return', '@throws'],
        ];
    }
}
