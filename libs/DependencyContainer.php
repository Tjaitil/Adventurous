<?php

namespace App\libs;

use \ReflectionClass;
use \ReflectionParameter;
use \Exception;
use \ReflectionMethod;

class DependencyContainer
{

    /**
     * Database instance
     *
     * @static
     */
    private static $instance = null;
    private $services = [];

    /**
     * Get database instance
     *
     * @return self
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get instance of class
     *
     * @param string $id
     *
     * @return instanceof $id
     */
    public function get($id)
    {

        try {

            $item = $this->resolve($id);

            // If item is not a instance of reflection class then return current item
            if (!($item instanceof ReflectionClass)) {
                return $item;
            }

            $instance = $this->getClassInstance($item);

            $this->set($id, $instance);

            return $instance;
        } catch (Exception $e) {
            throw new Exception("Error occured" . $e->getMessage() . $e->getLine());
        }
    }

    /**
     * Register service
     *
     * @param string $key
     * @param classInstance $value
     *
     * @return void
     */
    public function set(string $key, $value)
    {
        $this->services[$key] = $value;
    }

    /**
     * Resolve class instance
     *
     * @param string $id
     *
     * @return void
     */
    private function resolve($id)
    {
        $name = $id;
        if (isset($this->services[$id])) {
            $name = $this->services[$id];

            return $name;
        }
        return (new \ReflectionClass($name));
    }

    /**
     * Get instance of reflectionclass
     *
     * @param ReflectionClass $reflector
     *
     * @return void
     */
    private function getClassInstance(ReflectionClass $reflector)
    {
        $constructor = $reflector->getConstructor();


        if (is_null($constructor) || $constructor->getNumberOfRequiredParameters() == 0) {
            return $reflector->newInstance();
        }
        $params = [];

        // Resolve parameters
        foreach ($constructor->getParameters() as $param) {
            $type = $param->getType();
            if (!is_null($type) && !in_array($type->getName(), ["int", "array"])) {
                $params[] = $this->resolveParams($param);
            } else {
                throw new Exception("Could not resolve param " . $param->getName() . $reflector->getName());
            }
        }
        return $reflector->newInstanceArgs($params);
    }

    /**
     * Resolve class dependency parameter
     *
     * @param \ReflectionParameter $param
     *
     * @return void
     */
    private function resolveParams(ReflectionParameter $param)
    {
        if ($type = $param->getType()) {
            $instance = $this->get($type->getName());
            try {
                $this->set($type->getName(), $instance);
            } catch (\Exception $e) {
                throw new Exception("Could not resolve param " . $param->getName() . $e->getMessage());
            }
            return $instance;
        }
    }

    /**
     * Return parameters for a given method
     *
     * @param mixed $class Class instance
     * @param string $class_method Method name
     *
     * @return array
     */
    public function getMethodParameters($class, string $class_method)
    {
        $reflector = new ReflectionMethod($class::class, $class_method);
        foreach ($reflector->getParameters() as $param) {
            $params[] = $this->resolveParams($param);
        }
        return $params ?? [];
    }
}
