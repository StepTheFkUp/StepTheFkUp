<?php
declare(strict_types=1);

namespace EonX\EasyCfhighlander\Parameters;

use EonX\EasyCfhighlander\Interfaces\ParameterResolverInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;
use Symplify\PackageBuilder\Parameter\ParameterProvider;

final class ParameterResolver implements ParameterResolverInterface
{
    /**
     * @var string
     */
    private $cacheFile;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $filesystem;

    /**
     * @var callable[]
     */
    private $modifiers = [];

    /**
     * @var \Symplify\PackageBuilder\Parameter\ParameterProvider
     */
    private $parameterProvider;

    /**
     * @var callable[]
     */
    private $resolvers = [];

    public function __construct(Filesystem $filesystem, ParameterProvider $parameterProvider, ?string $cacheFile = null)
    {
        $this->filesystem = $filesystem;
        $this->parameterProvider = $parameterProvider;
        $this->cacheFile = $cacheFile ?? __DIR__ . '/../../var/last_params.yaml';
    }

    public function addModifier(string $param, callable $modifier): ParameterResolverInterface
    {
        if (isset($this->modifiers[$param]) === false) {
            $this->modifiers[$param] = [];
        }

        $this->modifiers[$param][] = $modifier;

        return $this;
    }

    public function addResolver(string $param, callable $resolver): ParameterResolverInterface
    {
        $this->resolvers[$param] = $resolver;

        return $this;
    }

    /**
     * @return mixed[]
     */
    public function resolve(InputInterface $input): array
    {
        $params = $this->resolveDefaultParameters();
        $cache = [];

        // Resolve params values
        foreach ($this->resolvers as $param => $resolver) {
            $resolved = \call_user_func($resolver, $params);

            $cache[$param] = $resolved;
            $params[$param] = $resolved;
        }

        // Apply modifiers
        foreach ($this->modifiers as $param => $modifiers) {
            foreach ($modifiers as $modifier) {
                $params[$param] = \call_user_func($modifier, $params);
            }
        }

        $params = \array_merge($params, $input->getArguments(), $input->getOptions());

        $this->filesystem->dumpFile($this->cacheFile, Yaml::dump($cache));

        return $params;
    }

    public function setCachePathname(string $pathname): ParameterResolverInterface
    {
        $this->cacheFile = $pathname;

        return $this;
    }

    /**
     * @return mixed[]
     */
    private function resolveDefaultParameters(): array
    {
        $params = [];

        if ($this->filesystem->exists($this->cacheFile)) {
            $params = Yaml::parseFile($this->cacheFile);
        }

        return \array_merge($this->parameterProvider->provide(), $params ?? []);
    }
}
