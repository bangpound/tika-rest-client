<?php

namespace Bangpound\Tika;

use Guzzle\Service\Command\OperationCommand;
use Guzzle\Service\Command\ResponseClassInterface;

class TikaResponse implements ResponseClassInterface, \ArrayAccess, \Countable, \Iterator
{
    use Metadata;

    private $xml;
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
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        $this->registerXPathNamespaces();
        $values = $this->xml->xpath('/default:html/default:body/default:div[@class="page"]['. $offset .']');

        return !empty($values);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        $this->registerXPathNamespaces();
        $values = $this->xml->xpath('/default:html/default:body/default:div[@class="page"]['. $offset .']');

        return array_pop($values);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     */
    public function offsetSet($offset, $value)
    {
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
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
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     */
    public function count()
    {
        $this->registerXPathNamespaces();

        return count($this->xml->xpath('/default:html/default:body/default:div[@class="page"]'));
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current()
    {
        return $this[$this->position];
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return isset($this[$this->position]);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->position = 1;
    }

    public function __toString()
    {
        $output = '';
        foreach ($this as $page) {
            $output .= $page->asXML();
        }
        return $output;
    }

    public function toArray()
    {
        $output = array();
        foreach ($this as $page) {
            $output[] = $page->asXML();
        }
        return $output;
    }
}
