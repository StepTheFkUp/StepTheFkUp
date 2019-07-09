<?php
declare(strict_types=1);

namespace LoyaltyCorp\EasyCfhighlander\Tests\Console\Commands;

use LoyaltyCorp\EasyCfhighlander\Tests\AbstractTestCase;

final class CloudFormationCommandTest extends AbstractTestCase
{
    /**
     * Ensure the .easy directory is only used if no existing files are present
     *
     * @return void
     *
     * @throws \Exception
     */
    public function testEasyDirectoryBackwardsCompatibility(): void
    {
        $inputs = [
            'project', // project
            'projectDatabase', // db_name
            'projectDatabaseUsername', // db_username
            'project.com', // dns_domain
            'true', // redis_enabled,
            'true', // elasticsearch_enabled
            'project', // ssm_prefix
            'project', // sqs_queue
            'aws_dev_account', // dev_account
            '599070804856', // ops_account
            'aws_prod_account' // prod_account
        ];

        $filesNotExisting = [
            '.easy/easy-docker-manifest.json',
            '.easy/easy-docker-params.yaml',
        ];

        $this->getFilesystem()->dumpFile(static::$cwd . '/' . 'easy-docker-manifest.json', '{}');
        $this->getFilesystem()->touch(static::$cwd . '/' . 'easy-docker-params.yaml');

        $this->executeCommand('code', $inputs);

        foreach ($filesNotExisting as $file) {
            self::assertFalse($this->getFilesystem()->exists(static::$cwd . '/' . $file));
        }
    }

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
            'project', // project
            'projectDatabase', // db_name
            'projectDatabaseUsername', // db_username
            'project.com', // dns_domain
            'true', // redis_enabled,
            'true', // elasticsearch_enabled
            'project', // ssm_prefix
            'project', // sqs_queue
            'aws_dev_account', // dev_account
            '599070804856', // ops_account
            'aws_prod_account' // prod_account
        ];

        $files = [
            'project-backend.cfhighlander.rb',
            'project-backend.config.yaml',
            'project-backend.mappings.yaml',
            'Jenkinsfile',
            'aurora.config.yaml',
            'az.mappings.yaml',
            'bastion.config.yaml',
            'ecs.config.yaml',
            'elasticsearch.config.yaml',
            'kms.config.yaml',
            'loadbalancer.config.yaml',
            'redis.config.yaml',
            'sqs.config.yaml',
            'vpc.config.yaml',
            'redis/redis.cfhighlander.rb',
            'redis/redis.cfndsl.rb',
            'redis/redis.config.yaml',
            'redis/redis.mappings.yaml'
        ];

        $display = $this->executeCommand('cloudformation', $inputs);

        self::assertContains(\sprintf('Generating files in %s:', \realpath(static::$cwd)), $display);

        foreach ($files as $file) {
            self::assertTrue($this->getFilesystem()->exists(static::$cwd . '/' . $file));
        }
    }
}
