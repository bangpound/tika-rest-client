<?php

namespace Bangpound\Tika;

use Guzzle\Common\Event;
use Guzzle\Service\Client as BaseClient;
use Guzzle\Service\Description\ServiceDescription;
use Guzzle\Service\Command\CommandInterface;
use Guzzle\Service\Exception\CommandException;

class Client extends BaseClient
{
    public function __construct($baseUrl = '', $config = null)
    {
        parent::__construct($baseUrl, $config);
        $service = ServiceDescription::factory(__DIR__ .'/tika.json');
        $this->setDescription($service);

        $this->getEventDispatcher()->addListener('command.before_prepare', function(Event $event) {

            /** @var CommandInterface $command */
            $command = $event['command'];

            // Change parameters of operations that use file parameter.
            if (in_array('file', $command->getOperation()->getParamNames())) {

                // Allow a file path to be passed instead of a file resource.
                if (!is_resource($command['file'])) {
                    if (file_exists($command['file'])) {
                        $command['file'] = fopen($command['file'], 'r');
                    } else {
                        throw new CommandException('File must exist.');
                    }
                }
            }
        });
    }
}
