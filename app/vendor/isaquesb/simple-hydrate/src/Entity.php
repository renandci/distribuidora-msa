<?php
namespace Simple\Hydrate;

abstract class Entity implements \JsonSerializable, \ArrayAccess
{
    use HydrateTrait, HydrateProtectedTrait, HydrateJsonTrait, HydrateArrayAccessTrait {
        HydrateProtectedTrait::hydrate insteadof HydrateTrait;
    }

    /**
     * Constructor
     * @param array|string $data
     */
    public function __construct($data = [])
    {
        if (is_array($data)) {
            $this->hydrate($data);
        }
    }

    public function __get($name)
    {
        return $this->offsetGet($name);
    }

    public function __set($name, $value)
    {
        $this->offsetSet($name, $value);
    }
}
