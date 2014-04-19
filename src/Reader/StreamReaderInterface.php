<?php

namespace ResearchGate\StreamSampling\Reader;


interface StreamReaderInterface {
    function get( $handle, $length );
}