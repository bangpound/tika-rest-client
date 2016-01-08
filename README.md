Tika REST Client
================

This PHP client interacts with the [Tika REST Server][TikaJAXRS] for extracting content
and metadata from a [wide variety of document file types][types]. There are [alternative
PHP libraries][alternatives] that use the [Tika command line client][TikaCLI], but
instantiating the JVM for each operation is slow and costly.

This client is built on [Guzzle](http://guzzlephp.org).

[TikaJAXRS]: http://wiki.apache.org/tika/TikaJAXRS
[TikaCLI]: http://tika.apache.org/1.4/gettingstarted.html
    "see "Using Tika as a command line utility""
[types]: http://tika.apache.org/1.4/formats.html
[alternatives]: https://packagist.org/search/?q=tika

## Project Setup

This project is installed with [composer][].

In the shell, you can run this command:

```shell
composer require bangpound/tika-rest-client
```

Or you can edit your `composer.json` file to include this requirement:

```json
{
    "require": {
        "bangpound/tika-rest-client": "^1.0"
    }
}
```

[composer]: http://getcomposer.org

## Usage

```php
<?php
$client = new Bangpound\Tika\Client('http://localhost:9998');
$response = $client->tika(array(
    'file' => 'TestPDF.pdf',
));

// Metadata varies by file and file type, so refer to the Apache Tika docs for details.
$all_metadata = $response->metadata;

// If you know the metadata element you want to retrieve, specify it as the argument
// to the response's metadata method.
$author = $response->metadata('author');

// Extracted content can be retrieved as a SimpleXMLElement or a string of XML.
$content_xml = $response->getBody();
$page_2 = $content_xml->children()->div[1];

$content_text = $response->getBody(true);
```

## Testing

The Tika REST Client has an incomplete suite of tests. Run them using phpunit after
installing the dev dependencies.

```shell
composer install
phpunit
```

## License

This code is released under the MIT license.
