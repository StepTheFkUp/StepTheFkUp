<?php

declare(strict_types=1);

namespace EonX\EasySsm\Console\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class DumpEnvCommand extends AbstractCommand
{
    /**
     * @var string[]
     */
    private static $excludes = [
        '_',
        'argc',
        'argv',
        'DOCUMENT_ROOT',
        'HOME',
        'PATH',
        'PATH_TRANSLATED',
        'PHP_SELF',
        'PWD',
        'REQUEST_TIME_FLOAT',
        'REQUEST_TIME',
        'SCRIPT_NAME',
        'SCRIPT_FILENAME',
        'SHLVL',
        'SHELL_VERBOSITY',
    ];

    protected function configure(): void
    {
        $this
            ->setName('dump-env')
            ->setDescription('Dump env vars in a PHP file to improve loading time.')
            ->addOption(
                'filename',
                'f',
                InputOption::VALUE_OPTIONAL,
                'File name to generate',
                \sprintf('%s/.env.local.php', \getcwd())
            )
            ->addOption(
                'excludes',
                'e',
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Env vars to exclude from dump',
                []
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $excludes = $input->getOption('excludes');
        $envs = $this->loadEnv($excludes);
        $vars = \var_export($envs, true);

        // Display env vars names when verbose and values when very verbose.
        if ($output->isVerbose()) {
            if ($output->isVeryVerbose() === false) {
                $output->writeln('Use the next level of verbosity to output env vars values');
            }

            foreach ($envs as $name => $value) {
                $output->writeln(\sprintf('- %s%s', $name, $output->isVeryVerbose() ? \sprintf(' = %s', $value) : ''));
            }
        }

        $contents = <<<EOF
<?php
declare(strict_types=1);

// This file was generated by running "easy-ssm dump-env"

return ${vars};

EOF;

        $filename = $input->getOption('filename');
        $this->filesystem->dumpFile($filename, $contents);

        $output->writeln(\sprintf('Successfully dumped env vars in <info>%s</info>', $filename));

        return 0;
    }

    /**
     * @param string[] $includes
     * @param string[] $excludes
     *
     * @return mixed[]
     */
    private function loadEnv(array $excludes): array
    {
        $env = $_ENV;
        $env += $_SERVER;

        foreach (\array_merge(static::$excludes, $excludes) as $exclude) {
            unset($env[$exclude]);
        }

        \ksort($env);

        return $env;
    }
}
