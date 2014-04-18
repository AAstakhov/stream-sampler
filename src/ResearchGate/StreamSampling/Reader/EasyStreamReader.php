<?php

namespace ResearchGate\StreamSampling\Reader;


class EasyStreamReader implements StreamReaderInterface{

    public function get( $handle, $length )
    {
        return fread($handle, $length);
    }
}