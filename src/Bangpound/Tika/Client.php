<?php

namespace Bangpound\Tika;

use Guzzle\Service\Client as BaseClient;
use Guzzle\Service\Description\ServiceDescription;

class Client extends BaseClient
{
    public function __construct($baseUrl = '', $config = null)
    {
        parent::__construct($baseUrl, $config);
        $service = ServiceDescription::factory(__DIR__ .'/tika.json');
        $this->setDescription($service);
    }
}
