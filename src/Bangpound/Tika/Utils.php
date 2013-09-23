<?php

namespace Bangpound\Tika;

use Guzzle\Service\Exception\CommandException;

class Utils
{
    public static function toResource($value)
    {

        // Allow a file path to be passed instead of a file resource.
        if (!is_resource($value)) {
            if (file_exists($value)) {
                return fopen($value, 'r');
            } else {
                throw new CommandException('File must exist.');
            }
        }

        return $value;
    }
}
