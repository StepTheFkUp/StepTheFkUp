<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('merge_sections', ['require', 'require-dev', 'autoload', 'autoload-dev', 'repositories', 'suggest']);

    $parameters->set('data_to_append', ['require-dev' => ['symplify/monorepo-builder' => '^7.2', 'symplify/changelog-linker' => '^7.2', 'phpstan/phpstan' => '^0.12.3', 'sensiolabs/security-checker' => '^5.0', 'symplify/easy-coding-standard' => '^7.2']]);

    $parameters->set('directories_to_repositories', [
        'packages/EasyApiToken' => 'git@github.com:eonx-com/easy-api-token.git', 'packages/EasyAsync' => 'git@github.com:eonx-com/easy-async.git', 'packages/EasyAwsCredentialsFinder' => 'git@github.com:eonx-com/easy-aws-credentials-finder.git', 'packages/EasyBugsnag' => 'git@github.com:eonx-com/easy-bugsnag.git', 'packages/EasyCfhighlander' => 'git@github.com:eonx-com/easy-cfhighlander.git', 'packages/EasyCore' => 'git@github.com:eonx-com/easy-core.git', 'packages/EasyDecision' => 'git@github.com:eonx-com/easy-decision.git', 'packages/EasyDocker' => 'git@github.com:eonx-com/easy-docker.git', 'packages/EasyEntityChange' => 'git@github.com:eonx-com/easy-entity-change.git', 'packages/EasyErrorHandler' => 'git@github.com:eonx-com/easy-error-handler.git', 'packages/EasyEventDispatcher' => 'git@github.com:eonx-com/easy-event-dispatcher.git', 'packages/EasyIdentity' => 'git@github.com:eonx-com/easy-identity.git', 'packages/EasyLock' => 'git@github.com:eonx-com/easy-lock.git', 'packages/EasyLogging' => 'git@github.com:eonx-com/easy-logging.git', 'packages/EasyNotification' => 'git@github.com:eonx-com/easy-notification.git', 'packages/EasyPipeline' => 'git@github.com:eonx-com/easy-pipeline.git', 'packages/EasyRepository' => 'git@github.com:eonx-com/easy-repository.git', 'packages/EasyPagination' => 'git@github.com:eonx-com/easy-pagination.git', 'packages/EasyPsr7Factory' => 'git@github.com:eonx-com/easy-psr7-factory.git', 'packages/EasyRandom' => 'git@github.com:eonx-com/easy-random.git', 'packages/EasySchedule' => 'git@github.com:eonx-com/easy-schedule.git', 'packages/EasySecurity' => 'git@github.com:eonx-com/easy-security.git', 'packages/EasySsm' => 'git@github.com:eonx-com/easy-ssm.git', 'packages/EasyStandard' => 'git@github.com:eonx-com/easy-standard.git', 'packages/EasyTest' => 'git@github.com:eonx-com/easy-test.git', 'packages/EasyWebhook' => 'git@github.com:eonx-com/easy-webhook.git']);
};
