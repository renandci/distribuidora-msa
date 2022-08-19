<?php
namespace Simple\Hydrate;

trait HydrateJsonTrait
{
    /**
     * @param \Closure $filter
     * @return array
     */
    public function toJson($filter = null)
    {
        $data = $this->toArray();
        foreach ($data as $key => $value) {
            if ($value instanceof \DateTime) {
                $value = str_replace(' ', 'T', $value->format('Y-m-d H:i:s'));
            } elseif ($value instanceof Entity) {
                $value = $value->toJson();
            } elseif (is_array($value)) {
                $newValue = [];
                foreach ($value as $row) {
                    if ($row instanceof Entity) {
                        $newValue[] = $row->toJson();
                    } else {
                        $newValue[] = $row;
                    }
                }
                $value = $newValue;
            }
            $data[$key] = $value;
        }
        $filter = !is_null($filter) && $filter instanceof \Closure ? $filter : function ($val) {
            return is_string($val) ? !empty($val) : !is_null($val);
        };
        $filtered = array_filter($data, $filter);
        if (!count($filtered)) {
            return null;
        }
        return $filtered;
    }

    function jsonSerialize()
    {
        return $this->toJson();
    }
}
