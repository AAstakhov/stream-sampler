<?php

namespace ResearchGate\StreamSampling\Input;

use ResearchGate\StreamSampling\StreamWrapper\RandomStreamWrapper;

class RandomInput implements InputInterface
{
    private $length;

    public function __construct( $length )
    {
        $this->length = (int)$length;

        if (!in_array( 'random', stream_get_wrappers() )) {
            stream_wrapper_register( RandomStreamWrapper::PROTOCOL, 'ResearchGate\StreamSampling\StreamWrapper\RandomStreamWrapper' );
        }
    }


    public function getResourcePath()
    {
        return RandomStreamWrapper::PROTOCOL . '://' . $this->length;
    }
}