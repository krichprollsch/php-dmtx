# php-dmtx

Datamatrix reader/writer based on [libdmtx](http://www.libdmtx.org/).

## Install

```
composer install
```

## Usage

```
use Dmtx\Writer

$writer = new Writer();

//encode message into file
$writer->encode('this is a message')
    ->saveAs('/tmp/image.png');

//encode message and output image 
echo $writer->encode('this is a message')
    ->dump();
```

## Test

```
phpunit
```

## Credits

Project structure inspired by
[Negotiation](https://github.com/willdurand/Negotiation) by
[willdurand](https://github.com/willdurand).

## License

php-dmtx is released under the MIT License. See the bundled LICENSE file for
details.
