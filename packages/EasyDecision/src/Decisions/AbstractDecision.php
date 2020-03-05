<?php
declare(strict_types=1);

namespace EonX\EasyDecision\Decisions;

use EonX\EasyDecision\Context;
use EonX\EasyDecision\Exceptions\ContextNotSetException;
use EonX\EasyDecision\Exceptions\ReservedContextIndexException;
use EonX\EasyDecision\Exceptions\UnableToMakeDecisionException;
use EonX\EasyDecision\Interfaces\ContextAwareInterface;
use EonX\EasyDecision\Interfaces\ContextInterface;
use EonX\EasyDecision\Interfaces\DecisionInterface;
use EonX\EasyDecision\Interfaces\DecisionOutputForRuleAwareInterface;
use EonX\EasyDecision\Interfaces\NonBlockingRuleErrorInterface;
use EonX\EasyDecision\Interfaces\RuleInterface;

abstract class AbstractDecision implements DecisionInterface
{
    /**
     * @var \EonX\EasyDecision\Interfaces\ContextInterface
     */
    protected $context;

    /**
     * @var mixed[]
     */
    protected $input;

    /**
     * @var null|mixed
     */
    private $defaultOutput;

    /**
     * @var string
     */
    private $name;

    /**
     * @var \EonX\EasyDecision\Interfaces\RuleInterface[]
     */
    private $rules = [];

    public function __construct(?string $name = null)
    {
        $this->name = $name ?? '<no-name>';
    }

    public function addRule(RuleInterface $rule): DecisionInterface
    {
        $this->rules[] = $rule;

        return $this;
    }

    /**
     * @param \EonX\EasyDecision\Interfaces\RuleInterface[] $rules
     */
    public function addRules(array $rules): DecisionInterface
    {
        foreach ($rules as $rule) {
            $this->addRule($rule);
        }

        return $this;
    }

    public function getContext(): ContextInterface
    {
        if ($this->context !== null) {
            return $this->context;
        }

        throw new ContextNotSetException($this->getExceptionMessage(
            'You cannot called getContext() before decision has been made'
        ));
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param mixed[] $input
     *
     * @return mixed
     *
     * @throws \EonX\EasyDecision\Exceptions\InvalidArgumentException
     * @throws \EonX\EasyDecision\Exceptions\UnableToMakeDecisionException
     */
    public function make(array $input)
    {
        // Index "context" cannot be used by users to avoid conflicts
        // because context is injected in expression language rules
        if (isset($input['context'])) {
            throw new ReservedContextIndexException($this->getExceptionMessage(
                '"context" is a reserved index it cannot be used'
            ));
        }

        $this->input = $input;
        $this->context = $context = new Context(\get_class($this), $input);

        // If no rules provided, return default output
        if (empty($this->rules)) {
            return $this->defaultOutput ?? $this->getDefaultOutput();
        }

        try {
            // Let children classes handle rules output and define the output
            return $this->processRules()->doMake();
        } catch (\Throwable $exception) {
            throw new UnableToMakeDecisionException(
                $this->getExceptionMessage($exception->getMessage()),
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * @param null|mixed $defaultOutput
     */
    public function setDefaultOutput($defaultOutput = null): DecisionInterface
    {
        $this->defaultOutput = $defaultOutput;

        return $this;
    }

    public function setName(string $name): DecisionInterface
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param mixed $output
     */
    abstract protected function doHandleRuleOutput($output): void;

    /**
     * @return mixed
     */
    abstract protected function doMake();

    /**
     * @return mixed
     */
    abstract protected function getDefaultOutput();

    protected function getExceptionMessage(string $message): string
    {
        return \sprintf('Decision "%s" of type "%s": %s', $this->name, \get_class($this), $message);
    }

    /**
     * @param mixed $output
     */
    private function addDecisionOutputForRule(RuleInterface $rule, $output): void
    {
        // Allow rules to customise decision output
        if ($rule instanceof DecisionOutputForRuleAwareInterface) {
            $output = $rule->getDecisionOutputForRule($output);
        }

        $this->context->addRuleOutput($rule->toString(), $output);
    }

    /**
     * @return \EonX\EasyDecision\Interfaces\RuleInterface[]
     */
    private function getRules(): array
    {
        // Sort rules by priority
        $rules = $this->rules;

        \usort($rules, function (RuleInterface $first, RuleInterface $second): int {
            return $first->getPriority() <=> $second->getPriority();
        });

        foreach ($rules as $rule) {
            if ($rule instanceof ContextAwareInterface) {
                $rule->setContext($this->context);
            }
        }

        return $rules;
    }

    private function processRules(): self
    {
        foreach ($this->getRules() as $rule) {
            // If propagation stopped, skip all the rules
            if ($this->context->isPropagationStopped()) {
                $this->addDecisionOutputForRule($rule, RuleInterface::OUTPUT_SKIPPED);
                continue;
            }

            // If rule doesn't support the input
            if ($rule->supports($this->input) === false) {
                $this->addDecisionOutputForRule($rule, RuleInterface::OUTPUT_UNSUPPORTED);
                continue;
            }

            try {
                $ruleOutput = $rule->proceed($this->input);

                $this->addDecisionOutputForRule($rule, $ruleOutput);

                // Let children classes handle the rule output
                $this->doHandleRuleOutput($ruleOutput);
            } catch (NonBlockingRuleErrorInterface $exception) {
                $this->addDecisionOutputForRule($rule, $exception->getErrorOutput());
            }
        }

        return $this;
    }
}
