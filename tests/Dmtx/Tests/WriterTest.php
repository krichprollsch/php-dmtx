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

    /** @var array */
    private $options;

    /** @var array */
    private $messages;

    protected function setUp(): void {
        $this->writer = new Writer($this->options ?? []);
        $this->writer->encode($this->messages ?? []);

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

    public static function imageTestProvider()
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
        $this->options = $options;
        $this->messages = $messages;
        $this->setUp();

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
        $this->options = $options;
        $this->messages = $messages;
        $this->setUp();

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
