<?php

use Guzzle\Tests\GuzzleTestCase;
use Guzzle\Service\Builder\ServiceBuilder;

error_reporting(E_ALL | E_STRICT);

// Ensure that composer has installed all dependencies
if (!file_exists(dirname(__DIR__) . '/composer.lock')) {
    die("Dependencies must be installed using composer:\n\nphp composer.phar install --dev\n\n"
        . "See http://getcomposer.org for help with installing composer\n");
}

// Include the composer autoloader
$autoloader = require dirname(__DIR__) . '/vendor/autoload.php';

// Set mock directory for Tika command responses.
GuzzleTestCase::setMockBasePath(__DIR__. DIRECTORY_SEPARATOR . 'mock');

// Add the services file to the default service builder
GuzzleTestCase::setServiceBuilder(ServiceBuilder::factory(array(
    'base' => array(
        'class' => 'Bangpound\\Tika\\Client',
    ),
    'test.tika' => array(
        'extends' => 'base',
        'params' => array(
            'base_url' => $_SERVER['TIKA_URL'],
        ),
    ),
    'test.mock' => array(
        'extends' => 'base',
        'params' => array(),
    ),
)));
