# php-dmtx

Datamatrix reader/writer based on [libdmtx](http://www.libdmtx.org/).

## Install

```sh
composer require "ptachoire/php-dmtx:*"
```

## Usage

```php
use Dmtx\Writer;

$writer = new Writer();

//encode message into file
$writer->encode('this is a message')
    ->saveAs('/tmp/image.png');

//encode message and output image 
echo $writer->encode('this is a message')
    ->dump();
```

```php
use Dmtx\Reader;

$reader = new Reader();

//decode message from data
$reader->decode($encoded_value);

//decode message from file 
echo $reader->decodeFile('/tmp/image.png');
```

## Test

```sh
composer install
./vendor/bin/phpunit
```

## Credits

Project structure inspired by
[Negotiation](https://github.com/willdurand/Negotiation) by
[willdurand](https://github.com/willdurand).

## License

php-dmtx is released under the MIT License. See the bundled LICENSE file for
details.
