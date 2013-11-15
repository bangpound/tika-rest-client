<?php

namespace Bangpound\Tika;

use Guzzle\Service\Client as BaseClient;
use Guzzle\Service\Description\ServiceDescription;

/**
 * \Guzzle\Service\Client::__call() handles undefined method calls.
 *
 * @method \Bangpound\Tika\Client greeting()
 * @method \Bangpound\Tika\Client version()
 * @method \Bangpound\Tika\Client tika(array $args)
 * @method \Bangpound\Tika\Client meta(array $args)
 * @method \Bangpound\Tika\Client unpacker(array $args)
 */
class Client extends BaseClient
{
    public function __construct($baseUrl = '', $config = null)
    {
        parent::__construct($baseUrl, $config);
        $service = ServiceDescription::factory(__DIR__ .'/tika.json');
        $this->setDescription($service);
    }
}
