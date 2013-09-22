<?php

namespace Bangpound\Tika;

use Guzzle\Service\Command\OperationCommand;
use Guzzle\Service\Command\ResponseClassInterface;

class MetaResponse implements ResponseClassInterface
{
    private $metadata = array();

    public function __construct($metadata = array())
    {
        $this->metadata = $metadata;
    }

    /**
     * Create a response model object from a completed command
     *
     * @param OperationCommand $command That serialized the request
     *
     * @return self
     */
    public static function fromCommand(OperationCommand $command)
    {
        $body = (string) $command->getResponse()->getBody();
        $lines = str_getcsv($body, "\n");
        $metadata = array();
        foreach ($lines as $line) {
            list($key, $value) = str_getcsv($line);
            $metadata[$key] = $value;
        }

        return new self($metadata);
    }

    /**
     * @param  string       $key Specify metadata key or method returns array of all metadata
     * @return string|array
     */
    public function metadata($key = null)
    {
        return $key ? $this->metadata[$key] : $this->metadata;
    }
}
