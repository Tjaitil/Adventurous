<?php

/**
 * @deprecated
 */
trait ModelFactory
{

    public static function getSelf()
    {

        // $args = resolveDependencies(self::class);
        $args = ServiceContainer::resolveDependencies(self::class);
        return new static(...$args);
        // return ServiceContainer::get(self::class);
    }
}

/**
 * Service Container to load dependencies
 */
class ServiceContainer
{
    /**
     * Instantiated services
     *
     * @var array
     */
    public static $services = [];


    /**
     * Get class with dependencies 
     *
     * @param String $className
     *
     * @return instanceof className
     */
    public static function get($className)
    {

        $item = ServiceContainer::resolve($className);
        if (!($item instanceof ReflectionClass)) {
            return $item;
        }
        $instance = ServiceContainer::getInstance($item);
        ServiceContainer::set($className, $instance);
        return $instance;
    }

    // public static function has($className)
    // {
    //     try {
    //         $item = ServiceContainer::resolve($className);
    //     } catch (Exception $e) {
    //         return false;
    //     }
    //     if ($item instanceof ReflectionClass) {
    //         return $item->isInstantiable();
    //     }
    //     return isset($item);
    // }


    public static function set(string $key, $value)
    {
        ServiceContainer::$services[$key] = $value;
    }

    private static function resolve($className)
    {
        try {
            $name = $className;
            if (isset(ServiceContainer::$services[$className])) {
                $name = ServiceContainer::$services[$className];
                if (is_callable($name)) {
                    return $name();
                }
            }
            return (new ReflectionClass($name));
        } catch (ReflectionException $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    public static function getInstance(ReflectionClass $item)
    {
        $constructor = $item->getConstructor();
        if (is_null($constructor) || $constructor->getNumberOfRequiredParameters() == 0) {
            return $item->newInstance();
        }
        $params = [];
        foreach ($constructor->getParameters() as $param) {
            if ($type = $param->getType()) {
                $instance = ServiceContainer::get($type->getName());
                $params[] = $instance;
                ServiceContainer::set($type->getName(), $instance);
            }
        }
        return $item->newInstanceArgs($params);
    }

    public static function resolveDependencies($className)
    {

        $reflection_class = new ReflectionClass($className);
        $constructor = $reflection_class->getConstructor();
        $args = [];
        foreach ($constructor->getParameters() as $param) {
            if ($type = $param->getType()) {
                $class = ServiceContainer::get($param->name);
                $args[] = $class;
            }
        }
        return $args;
    }
}

// function resolveDependencies($className)
// {
//     $args = [];
//     $reflection_class = new ReflectionClass($className);
//     // Check constructor params
//     foreach ($reflection_class->getConstructor()->getParameters() as $params) {
//         if ($params->name === "session") {
//             array_push($args, $_SESSION);
//         }
//         if (class_exists($params->name)) {
//             $class = getInstance(new ReflectionClass($params->name));
//             array_push($args, $class);
//         }
//     }
//     return $args;
// }


// class ServiceContainer {

//     public static $services = [];


// }


class a
{
    use ModelFactory;

    public function __construct(b $b, c $c)
    {
        var_dump($b);
        echo $c->hello;
        echo "a";
    }
}

class b
{
    use ModelFactory;

    public function __construct()
    {
        echo "b";
    }
}
class c
{
    use ModelFactory;
    public $hello = "Used factory";
    public function __construct(d $d)
    {
        echo $d->d;
        echo "c";
    }
}
class d
{
    use ModelFactory;

    public $d = "see you later";

    public function __construct()
    {
        echo "d";
    }
}

// a::getSelf();
