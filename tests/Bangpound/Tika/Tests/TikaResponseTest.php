<?php

namespace Bangpound\Tika\Tests;

/**
 * @covers Bangpound\Tika\TikaResponse
 */
class TikaResponseTest extends \Guzzle\Tests\GuzzleTestCase
{

    /**
     * Mock iterator
     *
     * This attaches all the required expectations in the right order so that
     * our iterator will act like an iterator!
     *
     * @param Iterator $iterator The iterator object; this is what we attach
     *      all the expectations to
     * @param array An array of items that we will mock up, we will use the
     *      keys (if needed) and values of this array to return
     * @param boolean $includeCallsToKey Whether we want to mock up the calls
     *      to "key"; only needed if you are doing foreach ($foo as $k => $v)
     *      as opposed to foreach ($foo as $v)
     */
    private function mockIterator(
        \Iterator $iterator,
        $items,
        $includeCallsToKey = FALSE
    )
    {
        $iterator->expects($this->at(0))
            ->method('rewind');
        $counter = 1;
        foreach ($items as $k => $v) {
            $iterator->expects($this->at($counter++))
                ->method('valid')
                ->will($this->returnValue(TRUE));
            $iterator->expects($this->at($counter++))
                ->method('current')
                ->will($this->returnValue($v));
            if ($includeCallsToKey) {
                $iterator->expects($this->at($counter++))
                    ->method('key')
                    ->will($this->returnValue($k));
            }
            $iterator->expects($this->at($counter++))
                ->method('next');
        }
        $iterator->expects($this->at($counter))
            ->method('valid')
            ->will($this->returnValue(FALSE));
    }

    public function testArrayAccess()
    {
        $client = $this->getServiceBuilder()->get('test.mock');
        $this->setMockResponse($client, array(
            'tika-response.txt',
        ));

        $file = __DIR__ .'/../../../../' . $_SERVER['TIKA_SRC_PATH'] . DIRECTORY_SEPARATOR .'testPDF_protected.pdf';

        /** @var \Bangpound\Tika\TikaResponse $response */
        $response = $client->tika(array('file' => $file));

        $this->assertContains('RETHINKING THE FINANCIAL NETWORK', $response[1]->asXML());
        $this->assertTrue(isset($response[1]));
        $this->assertFalse(isset($response[-1]));
    }

    public function testCountable()
    {
        $client = $this->getServiceBuilder()->get('test.mock');
        $this->setMockResponse($client, array(
            'tika-response.txt',
        ));

        $file = __DIR__ .'/../../../../' . $_SERVER['TIKA_SRC_PATH'] . DIRECTORY_SEPARATOR .'testPDF_protected.pdf';

        /** @var \Bangpound\Tika\TikaResponse $response */
        $response = $client->tika(array('file' => $file));

        $this->assertCount(41, $response);
    }

    public function testIterator()
    {
        $client = $this->getServiceBuilder()->get('test.mock');
        $this->setMockResponse($client, array(
            'tika-response.txt',
        ));

        $file = __DIR__ .'/../../../../' . $_SERVER['TIKA_SRC_PATH'] . DIRECTORY_SEPARATOR .'testPDF_protected.pdf';

        /** @var \Bangpound\Tika\TikaResponse $response */
        $response = $client->tika(array('file' => $file));
        foreach ($response as $key => $value) {
            $this->assertInternalType('integer', $key);
            $this->assertInstanceOf('SimpleXMLElement', $value);
        }
    }
}
