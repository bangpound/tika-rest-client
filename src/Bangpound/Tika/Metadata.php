<?php

namespace Bangpound\Tika;

trait Metadata
{
    private $metadata = array();

    /**
     * @param  string       $key Specify metadata key or method returns array of all metadata
     * @return string|array
     */
    public function metadata($key = null)
    {
        return $key ? $this->metadata[$key] : $this->metadata;
    }
}
