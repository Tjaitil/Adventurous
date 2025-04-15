<?php

namespace App\Services;

use Exception;
use Illuminate\Container\Container;
use Illuminate\Routing\ResolvesRouteDependencies;
use ReflectionMethod;

final class ConversationParamService
{
    use ResolvesRouteDependencies;

    /**
     * The container instance.
     *
     * @var \Illuminate\Container\Container
     */
    protected $container;

    /**
     * @var \App\Conversation\ServerEvents\BaseHandler
     */
    protected $handlerClass;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param  class-string  $conditional
     * @param  array<string, mixed>  $option_values
     */
    public function invokeServerEvent(string $conditional, array $option_values): bool
    {
        return (bool) $this->invokeMethod($conditional, $option_values);
    }

    /**
     * @param  class-string  $conditional
     * @param  array<string, mixed>  $option_values
     */
    public function invokeMethod(string $conditional, array $values): mixed
    {
        [$class, $className, $methodName] = $this->getClassInstance($conditional);
        if (! method_exists($class, $methodName)) {
            throw new Exception(sprintf('Method %s not found in class %s', $methodName, $className), 422);
        }

        $args = $this->resolveMethodDependencies($values,
            new ReflectionMethod($class, $methodName));

        $this->handlerClass = $className;

        return $class->{$methodName}(...array_values($args));
    }

    /**
     * @param  class-string  $conditional
     * @param  array<string, mixed>  $option_values
     */
    public function invokeConditional(string $conditional, array $option_values): bool
    {
        return (bool) $this->invokeMethod($conditional, $option_values);
    }

    /**
     * @param  class-string  $conditional
     * @param  array<string, mixed>  $option_values
     */
    public function invokeReplacer(string $conditional, array $option_values): string
    {
        return (string) $this->invokeMethod($conditional, $option_values);
    }

    /**
     * @return array{0: object, 1: class-string, 2: string}
     */
    private function getClassInstance(string $event): array
    {
        [$className, $methodName] = $this->getClassNameAndMethodName($event);
        if (! class_exists($className)) {
            throw new Exception(sprintf('Class %s not found', $className), 422);
        }

        return [app()->make($className), $className, $methodName];
    }

    /**
     * Get className and methodName from $eventString from the $event variable.
     *
     * @return array{0: string, 1: string}
     */
    protected function getClassNameAndMethodName(string $event): array
    {
        $eventParts = explode('@', $event);
        $className = 'App\\Conversation\\ServerEvents\\'.$eventParts[0];
        $methodName = $eventParts[1];
        $result = [$className, $methodName];

        return $result;
    }
}
