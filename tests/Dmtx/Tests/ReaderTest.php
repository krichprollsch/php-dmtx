<?php

namespace Dmtx\Tests;

use Dmtx\Reader;

class ReaderTest extends TestCase
{
    /** @var Reader */
    private $reader;

    protected function setUp(
        $options = []
    ) {
        $this->reader = new Reader($options);
    }

    public function imageTestProvider()
    {
        return [
            'simpleMessageShouldBeValid' => [
                [],
                ['yo'],
                dirname(__FILE__).'/rsc/yo.png'
            ],
            'multiMessagesShouldBeValid' => [
                [],
                ['yo','this','is','a','message'],
                dirname(__FILE__).'/rsc/yos.png'
            ]
        ];
    }

    /**
     * @dataProvider imageTestProvider
     * @param array $options
     * @param array $expected_messages
     * @param $filename
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
     * @param array $options
     * @param array $expected_messages
     * @param $filename
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
