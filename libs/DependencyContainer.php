<?php

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
            var_dump($e->getMessage());
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
        return (new ReflectionClass($name));
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
                throw new Exception("Could not resolve param " . $param->getName());
            }
        }
        return $reflector->newInstanceArgs($params);
    }

    private function resolveParams($param)
    {
        if ($type = $param->getType()) {
            $instance = $this->get($type->getName());
            $this->set($type->getName(), $instance);
            return $instance;
        }
    }

    public function getMethodParameters($class, $class_method)
    {
        $reflector = new ReflectionMethod($class::class, $class_method);
        foreach ($reflector->getParameters() as $param) {
            $params[] = $this->resolveParams($param);
        }
        return $params ?? [];
    }
}
