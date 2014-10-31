<?php

namespace Dmtx\Tests;

use Dmtx\Reader;

class ReaderTest extends TestCase
{
    private $reader;

    protected function setUp(
        $options = array()
    ) {
        $this->reader = new Reader($options);
    }

    public function imageTestProvider()
    {
        return array(
            'simpleMessageShouldBeValid' => array(
                array(),
                array('yo'),
                dirname(__FILE__).'/rsc/yo.png'
            ),
            'multiMessagesShouldBeValid' => array(
                array(),
                array('yo','this','is','a','message'),
                dirname(__FILE__).'/rsc/yos.png'
            )
        );
    }

    /**
     * @dataProvider imageTestProvider
     */
    public function testDecodeShouldReturnValidMessage(
        array $options,
        array $expected_messages,
        $filename
    ) {
        $this->setUp($options);

        $this->assertEquals(
            implode(" ", $expected_messages),
            $this->reader->decode(file_get_contents($filename))
        );
    }

    /**
     * @dataProvider imageTestProvider
     */
    public function testDecodeFileShouldReturnValidMessage(
        array $options,
        array $expected_messages,
        $filename
    ) {
        $this->setUp($options);

        $this->assertEquals(
            implode(" ", $expected_messages),
            $this->reader->decodeFile($filename)
        );
    }
}
