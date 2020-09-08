<?php

declare(strict_types=1);

namespace EonX\EasyStandard\Tests\Sniffs\Commenting\FunctionCommentSniff;

use EonX\EasyStandard\Sniffs\Commenting\FunctionCommentSniff;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @covers \EonX\EasyStandard\Sniffs\Commenting\FunctionCommentSniff
 */
final class FunctionCommentSniffTest extends AbstractCheckerTestCase
{
    /**
     * @return iterable<mixed>
     *
     * @throws \Symplify\SmartFileSystem\Exception\FileNotFoundException
     */
    public function provideCorrectFixtures(): iterable
    {
        yield [new SmartFileInfo(__DIR__ . '/Fixture/Correct/correct.php.inc')];
    }

    /**
     * @return iterable<mixed>
     *
     * @throws \Symplify\SmartFileSystem\Exception\FileNotFoundException
     */
    public function provideWrongFixtures(): iterable
    {
        yield [new SmartFileInfo(__DIR__ . '/Fixture/Wrong/missingDocComment.php.inc'), 1];
        yield [new SmartFileInfo(__DIR__ . '/Fixture/Wrong/incorrectCommentStyle.php.inc'), 1];
        yield [new SmartFileInfo(__DIR__ . '/Fixture/Wrong/blankLineAfterComment.php.inc'), 2];
        yield [new SmartFileInfo(__DIR__ . '/Fixture/Wrong/missingParamDocComment.php.inc'), 1];
    }

    /**
     * @dataProvider provideCorrectFixtures()
     */
    public function testCorrectSniffs(SmartFileInfo $fileInfo): void
    {
        $this->doTestCorrectFileInfo($fileInfo);
    }

    /**
     * @dataProvider provideWrongFixtures()
     */
    public function testWrongSniffs(SmartFileInfo $wrongFileInfo, int $expectedErrorCount): void
    {
        $this->doTestFileInfoWithErrorCountOf($wrongFileInfo, $expectedErrorCount);
    }

    protected function getCheckerClass(): string
    {
        return FunctionCommentSniff::class;
    }
}
