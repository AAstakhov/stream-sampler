<?php

namespace ResearchGate\StreamSampling\Tests\Stream;

use ResearchGate\StreamSampling\Stream\Stream;
use ResearchGate\StreamSampling\Stream\StreamSampler;
use ResearchGate\StreamSampling\Input;

class StreamSamplerTest extends \PHPUnit_Framework_TestCase {

    public function testBehaviour()
    {
        $sampler = new StreamSampler();

        $input = new Input\TextInput( 'THEQUICKBROWNFOXJUMPSOVERTHELAZYDOG' );
        $stream = new Stream($input, false);

        $k = 5;
        $sample = $sampler->getSample($k, $stream);
        $this->assertEquals(strlen($sample), $k);
    }

}
