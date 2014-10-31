<?php

namespace Dmtx\Tests;

use Dmtx\Writer;

class WriterTest extends TestCase
{
    private $writer;

    protected function setUp(
        $options = array(),
        $messages = array()
    ) {
        $this->writer = new Writer($options);
        $this->writer->encode($messages);
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
        array $options,
        array $messages,
        $expected_filename
    ) {
        $this->setUp($options, $messages);

        $this->assertEquals(
            file_get_contents($expected_filename),
            $this->writer->dump()
        );
    }

    /**
     * @dataProvider imageTestProvider
     */
    public function testSaveAsShouldCreateValidFile(
        array $options,
        array $messages,
        $expected_filename
    ) {
        $this->setUp($options, $messages);

        $tmpfile = tempnam(sys_get_temp_dir(), 'dmtx-test-unit-').'.png';

        $this->writer->saveAs($tmpfile);

        $this->assertFileEquals(
            $expected_filename,
            $tmpfile
        );
    }
}
