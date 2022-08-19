<?php

use Simple\Hydrate;

class TestObject extends Hydrate\Entity
{
    use Hydrate\HydrateConstructorTrait;

    /**
     * Name
     * @var string
     */
    protected $name;

    /**
     * Age
     * @var int
     */
    protected $age = 18;

    /**
     * Constructor
     * @param array|string $data
     */
    public function __construct($data = [])
    {
        if (is_array($data)) {
            parent::__construct($data);
        } else {
            $args = func_get_args();
            $this->hydrateByArgs(['name', 'age'], $args);
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return ucfirst(strtolower($this->name));
    }

    /**
     * @return int
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * @param int $age
     * @return self
     */
    public function setAge($age)
    {
        $filterNum = preg_replace('/[^\d]/', '', $age);
        $this->age = $filterNum;
        return $this;
    }
}
