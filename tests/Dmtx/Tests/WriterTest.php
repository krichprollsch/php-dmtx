<?php

namespace Dmtx\Tests;

use Dmtx\Writer;
use Dmtx\Reader;

class WriterTest extends TestCase
{
    /** @var Writer */
    private $writer;
    /** @var Reader */
    private $reader;

    protected function setUp(
        $options = [],
        $messages = []
    ) {
        $this->writer = new Writer($options);
        $this->writer->encode($messages);

        $this->reader = new Reader();
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
        return [
            'simpleMessageShouldBeValid' => [
                [
                    'encoding' => 'ascii',
                    'module' => 5,
                    'symbol-size' => 'square-auto',
                    'format' => 'png'
                ],
                ['yo'],
                'yo'
            ],
            'multiMessagesShouldBeValid' => [
                [
                    'encoding' => 'ascii',
                    'module' => 5,
                    'symbol-size' => 'square-auto',
                    'format' => 'png',
                    'message-separator' => ' '
                ],
                ['yo','this','is','a','message'],
                'yo this is a message'
            ]
        ];
    }

    /**
     * @dataProvider imageTestProvider
     * @param array $options
     * @param array $messages
     * @param $expected
     */
    public function testDumpShouldReturnValidImage(
        array $options,
        array $messages,
        $expected
    ) {
        $this->setUp($options, $messages);

        $this->assertEquals(
            $expected,
            $this->reader->decode(
                $this->writer->dump()
            )
        );
    }

    /**
     * @dataProvider imageTestProvider
     * @param array $options
     * @param array $messages
     * @param $expected
     */
    public function testSaveAsShouldCreateValidFile(
        array $options,
        array $messages,
        $expected
    ) {
        $this->setUp($options, $messages);

        $tmpfile = tempnam(sys_get_temp_dir(), 'dmtx-test-unit-').'.png';

        $this->writer->saveAs($tmpfile);

        $this->assertFileExists($tmpfile);

        $this->assertEquals(
            $expected,
            $this->reader->decode(
                file_get_contents($tmpfile)
            )
        );

        unlink($tmpfile);
    }
}
