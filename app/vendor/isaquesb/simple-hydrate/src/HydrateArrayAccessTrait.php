<?php
namespace Simple\Hydrate;

trait HydrateArrayAccessTrait
{
    public function offsetExists($offset)
    {
        return isset($this->$offset);
    }

    public function offsetGet($offset)
    {
        $method = 'get' . ucfirst($offset);
        if (method_exists($this, $method)) {
            return $this->{$method}();
        }
        return $this->{$offset};
    }

    public function offsetSet($offset, $value)
    {
        $method = 'set' . ucfirst($offset);
        if (method_exists($this, $method)) {
            $this->{$method}($value);
            return;
        }
        $this->{$offset} = $value;
    }

    public function offsetUnset($offset)
    {
        $this->offsetSet($offset, null);
        unset($this->{$offset});
    }
}
