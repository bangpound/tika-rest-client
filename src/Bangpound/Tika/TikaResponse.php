<?php

namespace Bangpound\Tika;

use Guzzle\Service\Command\OperationCommand;
use Guzzle\Service\Command\ResponseClassInterface;

class TikaResponse implements ResponseClassInterface
{
    private $xml;

    /**
     * @param OperationCommand $command
     *
     * @return TikaResponse
     */
    public static function fromCommand(OperationCommand $command)
    {
        $response = $command->getResponse();
        $xml = $response->xml();

        return new self($xml);
    }

    /**
     * @param \SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->xml = $xml;
    }

    /**
     * Get the response head element
     *
     * @param bool $asString Set to TRUE to return a string of the head rather than a SimpleXMLElement
     *
     * @return \SimpleXMLElement|string
     */
    public function getHead($asString = false)
    {
        return $asString ? $this->xml->head->asXML() : $this->xml->head;
    }

    /**
     * Get the response body element
     *
     * @param bool $asString Set to TRUE to return a string of the body rather than a SimpleXMLElement
     *
     * @return \SimpleXMLElement|string
     */
    public function getBody($asString = false)
    {
        return $asString ? $this->xml->body->asXML() : $this->xml->body;
    }
}
