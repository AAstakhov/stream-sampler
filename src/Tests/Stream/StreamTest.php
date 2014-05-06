<?php

namespace ResearchGate\StreamSampling\Tests;

use ResearchGate\StreamSampling\Input;
use ResearchGate\StreamSampling\Stream\Stream;

class StreamTest extends \PHPUnit_Framework_TestCase
{
    public function testGetUtf8Data()
    {
        $input   = new Input\TextInput( 'БыстраяКоричневаяЛисаПрыгаетЧерезЛенивогоПса' );
        $stream  = new Stream( $input, true );
        $portion = $stream->get( 7 );
        $this->assertEquals( $portion, 'Быстрая' );

        $portion = $stream->get( 10 );
        $this->assertEquals( $portion, 'Коричневая' );
    }

    public function testGetDataFromRandomStream()
    {
        $input  = new Input\RandomInput( 100 );
        $stream = new Stream( $input, false );
        $text   = '';
        while (!$stream->isEnd()) {
            $text .= $stream->get( 10 );
        }

        // Read buffer for PHP stream wrapper equals to 8192
        // it was read 5 times
        $this->assertEquals( strlen( $text ), 8192 * 5 );
    }

    public function testFileInput()
    {
        $input  = new Input\FileInput( __DIR__ . '/../res/utf8_text.txt' );
        $stream = new Stream( $input, true );
        $stream->get( 50 ); // Pass first paragraph
        $textInFile = $stream->get( 16 );
        $this->assertEquals( $textInFile, 'Социальные медиа' );
    }

    public function testNonExistingFileInput()
    {
        $this->setExpectedException( 'Exception' );
        $input  = new Input\FileInput( 'this file does not exist' );
        $stream = new Stream( $input, false );
    }

}
