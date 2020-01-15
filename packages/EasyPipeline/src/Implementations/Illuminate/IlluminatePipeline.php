<?php
declare(strict_types=1);

namespace EonX\EasyPipeline\Implementations\Illuminate;

use EonX\EasyPipeline\Exceptions\EmptyMiddlewareListException;
use EonX\EasyPipeline\Interfaces\MiddlewareLoggerAwareInterface;
use EonX\EasyPipeline\Interfaces\MiddlewareLoggerInterface;
use EonX\EasyPipeline\Interfaces\PipelineInterface;
use Illuminate\Contracts\Pipeline\Pipeline as IlluminatePipelineContract;

final class IlluminatePipeline implements PipelineInterface, MiddlewareLoggerInterface
{
    /**
     * @var \Illuminate\Contracts\Pipeline\Pipeline
     */
    private $illuminatePipeline;

    /**
     * @var mixed[]
     */
    private $logs = [];

    /**
     * @var mixed[]
     */
    private $middlewareList;

    /**
     * IlluminatePipeline constructor.
     *
     * @param \Illuminate\Contracts\Pipeline\Pipeline $illuminatePipeline
     * @param mixed[] $middlewareList
     *
     * @throws \EonX\EasyPipeline\Exceptions\EmptyMiddlewareListException
     */
    public function __construct(IlluminatePipelineContract $illuminatePipeline, array $middlewareList)
    {
        if (empty($middlewareList)) {
            throw new EmptyMiddlewareListException(\sprintf(
                'In %s, given middleware list is empty',
                \get_class($this)
            ));
        }

        $this->illuminatePipeline = $illuminatePipeline;
        $this->middlewareList = $middlewareList;
    }

    /**
     * Return logs created by each middleware during process.
     *
     * @return mixed[]
     *
     * @throws \EonX\EasyPipeline\Exceptions\PipelineDidntRunException If called before process() is called
     */
    public function getLogs(): array
    {
        return $this->logs;
    }

    /**
     * Log given content for given middleware.
     *
     * @param string $middleware
     * @param mixed $content
     *
     * @return void
     */
    public function log(string $middleware, $content): void
    {
        if (isset($this->logs[$middleware]) === false) {
            $this->logs[$middleware] = [];
        }

        $this->logs[$middleware][] = $content;
    }

    /**
     * Process set input through set middleware list and return processed input.
     *
     * @param mixed $input The input to be processed
     *
     * @return mixed
     */
    public function process($input)
    {
        $this->logs = []; // Reset logs to allow same pipeline to process multiple inputs

        // Handle middleware logger aware
        foreach ($this->middlewareList as $middleware) {
            if ($middleware instanceof MiddlewareLoggerAwareInterface) {
                $middleware->setLogger($this);
            }
        }

        return $this->illuminatePipeline
            ->send($input)
            ->through($this->middlewareList)
            ->via('handle')
            ->then(function ($input) {
                return $input;
            });
    }
}
