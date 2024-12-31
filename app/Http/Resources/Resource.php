<?php

namespace App\Http\Resources;

use Illuminate\Database\Eloquent\Model;

abstract class Resource
{
    protected array $default;

    public function __construct($resource = null, $data = null)
    {
        if ($data instanceof Model) {
            $data = $data->toArray();
        }

        if (is_array($resource)) {
            $this->default = $resource;
        } else if (is_string($resource)) {
            $this->default = json_decode($resource, true);
        }
        $this->setDefaultData($data);
    }

    public function setDefaultData($data)
    {
        if (is_array($data)) {
            $new = static::mapping($data);
            $this->default = array_merge($this->default, $new);
        }
    }

    /**
     * Map data to array with objects
     * Used to respect other ressourceclasses within a given ressource
     *
     * @param array $data
     *
     * @return array
     */
    abstract public static function mapping(array $data): array;


    /**
     *
     * @param array $data
     *
     * @return array
     */
    abstract public function toArray(): array;

    public function __get($name)
    {
        return $this->default[$name];
    }

    public function __set($name, $value)
    {
            $this->default[$name] = $value;
    }
}
