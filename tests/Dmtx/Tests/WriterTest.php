<?php

namespace Dmtx\Tests;

use Dmtx\Writer;

class WriterTest extends TestCase
{
    private $writer;

    protected function setUp($options = array())
    {
        $this->writer = new Writer($options);
    }

    public function testEncodeShouldReturnWriterInstance()
    {
        $this->assertSame(
            $this->writer,
            $this->writer->encode('yo')
        );
    }

    public function testDumpShouldReturnString()
    {
        $this->assertTrue(
            is_string(
                $this->writer
                    ->encode('yo')
                    ->dump()
            )
        );
    }

    public function imageTestProvider()
    {
        return array(
            'simpleMessageShouldBeValid' => array(
                array(
                    'encoding' => 'ascii',
                    'module' => 5,
                    'symbol-size' => 'square-auto',
                    'format' => 'png'
                ),
                array('yo'),
                dirname(__FILE__).'/rsc/yo.png'
            ),
            'multiMessagesShouldBeValid' => array(
                array(
                    'encoding' => 'ascii',
                    'module' => 5,
                    'symbol-size' => 'square-auto',
                    'format' => 'png',
                    'message-separator' => ' '
                ),
                array('yo','this','is','a','message'),
                dirname(__FILE__).'/rsc/yos.png'
            )
        );
    }

    /**
     * @dataProvider imageTestProvider
     */
    public function testDumpShouldReturnValidImage(
        $options,
        array $messages,
        $expected_filename
    ) {

        $this->setUp($options);

        foreach ($messages as $message) {
            $this->writer->encode($message);
        }

        $this->assertEquals(
            file_get_contents($expected_filename),
            $this->writer->dump()
        );
    }
}
