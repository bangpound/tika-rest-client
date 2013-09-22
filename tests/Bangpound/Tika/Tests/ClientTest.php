<?php

namespace Bangpound\Tika\Tests;

use Bangpound\Tika\Client;
use Guzzle\Http\Message\Response;

/**
 * @group server
 * @covers Bangpound\Tika\Client
 */
class ClientTest extends \Guzzle\Tests\GuzzleTestCase
{
    public function testVersion()
    {
        $client = $this->getServiceBuilder()->get('test.mock');
        $this->setMockResponse($client, array('version.txt'));

        /** @var \Guzzle\Http\Message\Response $response */
        $response = $client->version();
        $version = $response->getBody(TRUE);
        $this->assertRegExp('/^Apache Tika (\d+\\.)?(\d+\\.)?(\\*|\d+)$/', $version);
    }

    public function testGreeting()
    {
        $client = $this->getServiceBuilder()->get('test.mock');
        $this->setMockResponse($client, array('greeting.txt'));

        /** @var \Guzzle\Http\Message\Response $response */
        $response = $client->greeting();
        $greeting = $response->getBody(TRUE);
        $this->assertContains('This is Tika Server. Please PUT', $greeting);
    }

    public function testTika()
    {
        $client = $this->getServiceBuilder()->get('test.mock');
        $this->setMockResponse($client, array(
            'tika.txt',
        ));

        $file = __DIR__ .'/../../../../' . $_SERVER['TIKA_SRC_PATH'] . DIRECTORY_SEPARATOR .'testPDF.pdf';

        /** @var \Bangpound\Tika\TikaResponse $response */
        $response = $client->tika(array('file' => $file));

        $this->assertEquals('application/pdf', $response->metadata('Content-Type'));
        $this->assertEquals('Bertrand Delacrétaz', $response->metadata('creator'));
        $this->assertEquals('Bertrand Delacrétaz', $response->metadata('Author'));
        $this->assertEquals('Firefox', $response->metadata('xmp:CreatorTool'));
        $this->assertEquals('Apache Tika - Apache Tika', $response->metadata('dc:title'));

        $this->assertEquals('2007-09-15T09:02:31Z', $response->metadata('date'));
        $this->assertEquals('2007-09-15T09:02:31Z', $response->metadata('modified'));

        $content = $response->getBody(TRUE);
        $this->assertContains('Apache Tika', $content);
        $this->assertContains('Tika - Content Analysis Toolkit', $content);
        $this->assertContains('incubator', $content);
        $this->assertContains('Apache Software Foundation', $content);
        $this->assertNotContains('ToolkitApache', $content, 'should have word boundary after headline');
        $this->assertNotContains('libraries.Apache', $content, 'should have word boundary between paragraphs');
    }
}
