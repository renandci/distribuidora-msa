<?php
namespace Simple\Hydrate;

trait HydrateProtectedTrait
{

    /**
     * Proxies Data Hydrate
     * @param array $data
     * @return object
     */
    protected function hydrate(array $data = [])
    {
        return $this->doHydrate($data);
    }
}
