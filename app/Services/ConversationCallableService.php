<?php

namespace App\Services;

use App\Attributes\SelectedConversationOptionValue;
use App\Conversation\Handlers\BaseHandler;
use Exception;
use Illuminate\Container\Container;
use Illuminate\Routing\ResolvesRouteDependencies;
use ReflectionClass;
use ReflectionMethod;

final class ConversationCallableService
{
    use ResolvesRouteDependencies;

    /**
     * The container instance.
     *
     * @var \Illuminate\Container\Container
     */
    protected $container;

    protected BaseHandler $handlerClass;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param  array<array-key, mixed>|null  $selectedOptionValues
     */
    public function invokeServerEvent(BaseHandler $handler, string $methodName, ?array $selectedOptionValues): string
    {
        return (string) $this->invokeMethod($handler, $methodName, [], $selectedOptionValues);
    }

    /**
     * @param  array<string, mixed>  $optionValues
     * @param  array<array-key, mixed>|null  $selectedOptionValues
     */
    private function invokeMethod(BaseHandler $handler, string $methodName,
        array $optionValues, ?array $selectedOptionValues): mixed
    {
        if (! method_exists($handler, $methodName)) {
            throw new Exception(sprintf('Method %s not found in class %s', $methodName, $handler::class), 422);
        }

        $ReflectionClass = new ReflectionClass($handler::class);
        $parameters = $ReflectionClass->getMethod($methodName)->getParameters();
        foreach ($parameters as $parameter) {
            foreach ($parameter->getAttributes() as $key => $attribute) {
                if ($attribute->getName() === SelectedConversationOptionValue::class) {
                    if (! isset($selectedOptionValues[$parameter->getName()])) {
                        throw new Exception(sprintf('Parameter %s not found in class %s', $parameter->getName(), $handler::class), 422);
                    }
                    $optionValues[$parameter->getName()] = $selectedOptionValues[$parameter->getName()];
                }
            }
        }

        $args = $this->resolveMethodDependencies($optionValues,
            new ReflectionMethod($handler, $methodName));

        $this->handlerClass = $handler;

        return $handler->{$methodName}(...array_values($args));
    }

    /**
     * @param  array<string, mixed>  $optionValues
     * @param  array<string, mixed>  $selectedOptionValues
     */
    public function invokeConditional(BaseHandler $handler, string $methodName, array $optionValues, array $selectedOptionValues): bool
    {
        return (bool) $this->invokeMethod($handler, $methodName, $optionValues, $selectedOptionValues);
    }

    /**
     * @param  array<string, mixed>  $trackedOptionValue
     */
    public function invokeReplacer(BaseHandler $handler, string $methodName, array $trackedOptionValue): string
    {
        return (string) $this->invokeMethod($handler, $methodName, [], $trackedOptionValue);
    }
}
