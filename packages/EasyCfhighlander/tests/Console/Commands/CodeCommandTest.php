<?php
declare(strict_types=1);

namespace LoyaltyCorp\EasyCfhighlander\Tests\Console\Commands;

use LoyaltyCorp\EasyCfhighlander\Tests\AbstractTestCase;

final class CodeCommandTest extends AbstractTestCase
{
    /**
     * Command should generate cloudformation files.
     *
     * @return void
     *
     * @throws \Exception
     */
    public function testGenerateCloudFormationFiles(): void
    {
        $inputs = [
            'project',
            'project.com',
            'aws_dev_account',
            '599070804856',
            'aws_prod_account'
        ];

        $files = [
            'Jenkinsfile.twig',
            'gcs.cfhighlander.rb',
            'gcs.config.yaml',
            'project-schema.cfhighlander.rb',
            'project-schema.config.yaml'
        ];

        $display = $this->executeCommand('code', $inputs);

        foreach ($inputs as $input) {
            self::assertContains($input, $display);
        }

        self::assertContains(\sprintf('Generating files in %s:', \realpath(static::$cwd)), $display);

        foreach ($files as $file) {
            self::assertTrue($this->getFilesystem()->exists(static::$cwd . '/' . $file));
            self::assertContains($file, $display);
        }
    }
}
