<?php

namespace ResearchGate\StreamSampling\Input;

class RandomInput implements InputInterface
{
    private $length;

    public function __construct($length)
    {
        $this->length = (int)$length;
        stream_wrapper_register('random', 'ResearchGate\StreamSampling\StreamWrapper\RandomStreamWrapper');
    }


    public function getResourcePath()
    {
        return 'random://' . $this->length;
    }
}