<?php

namespace Bangpound\Tika;

use Guzzle\Service\Command\OperationCommand;
use Guzzle\Service\Command\ResponseClassInterface;

/**
 * Class TikaResponse
 * @package Bangpound\Tika
 */
class TikaResponse extends Metadata implements ResponseClassInterface, \ArrayAccess, \Countable, \Iterator, \Serializable
{
    /**
     * @var \SimpleXMLElement
     */
    private $xml;

    /**
     * @var int
     */
    private $position = 1;

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
     * @param  \SimpleXMLElement            $xml
     * @return \Bangpound\Tika\TikaResponse
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->xml = $xml;
        $this->metadata = array();
        $this->position = 1;
        /** @var \SimpleXMLElement $child */
        foreach ($this->getHead()->children()->meta as $child) {
            $key = (string) $child['name'];
            $value = (string) $child['content'];
            $this->metadata[$key] = $value;
        }
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

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        $this->registerXPathNamespaces();
        $values = $this->xml->xpath('/default:html/default:body/default:div[@class="page"]['. $offset .']');

        return !empty($values);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        $this->registerXPathNamespaces();
        $values = $this->xml->xpath('/default:html/default:body/default:div[@class="page"]['. $offset .']');

        return array_pop($values);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
    }

    private function registerXPathNamespaces()
    {
        $namespaces = $this->xml->getNamespaces();
        foreach ($namespaces as $prefix => $ns) {
            if ($prefix === '') {
                $this->xml->registerXPathNamespace('default', $ns);
            } else {
                $this->xml->registerXPathNamespace($prefix, $ns);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        $this->registerXPathNamespaces();

        return count($this->xml->xpath('/default:html/default:body/default:div[@class="page"]'));
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return $this[$this->position];
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return isset($this[$this->position]);
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->position = 1;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $output = '';
        foreach ($this as $page) {
            $output .= $page->asXML();
        }

        return $output;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $output = array();
        foreach ($this as $page) {
            $output[] = $page->asXML();
        }

        return $output;
    }

    /**
     * @return mixed
     */
    public function serialize()
    {
        return $this->xml->asXML();
    }

    /**
     * @param string $xml
     */
    public function unserialize($xml)
    {
        $this->xml = simplexml_load_string($xml);
    }
}
