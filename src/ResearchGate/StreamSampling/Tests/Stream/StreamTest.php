<?php

namespace ResearchGate\StreamSampling\Tests;

use ResearchGate\StreamSampling\Input\TextInput;
use ResearchGate\StreamSampling\Input\RandomInput;
use ResearchGate\StreamSampling\Input\FileInput;
use ResearchGate\StreamSampling\Stream\Stream;

class StreamTest extends \PHPUnit_Framework_TestCase
{
    public function testGetUtf8Data()
    {
        $input   = new TextInput( 'БыстраяКоричневаяЛисаПрыгаетЧерезЛенивогоПса' );
        $stream  = new Stream( $input, true );
        $portion = $stream->get( 7 );
        $this->assertEquals( $portion, 'Быстрая' );
        $portion = $stream->get( 10 );
        $this->assertEquals( $portion, 'Коричневая' );
    }

    public function testGetDataFromRandomStream()
    {
        $input  = new RandomInput( 100 );
        $stream = new Stream( $input, false );
        $text   = '';
        while (!$stream->isEnd()) {
            $text .= $stream->get( 10 );
        }

        // Read buffer for PHP stream wrapper equals to 8192
        // it was read 5 times
        $this->assertEquals( strlen( $text ), 8192 * 5 );
    }

}
