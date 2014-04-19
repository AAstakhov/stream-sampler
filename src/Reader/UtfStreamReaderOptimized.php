<?php

namespace ResearchGate\StreamSampling\Reader;

class UtfStreamReaderOptimized implements StreamReaderInterface
{

    public function get( $handle, $length )
    {
        $data = fread( $handle, $length * 4 );
        fseek( $handle, mb_strlen( $data ) - $length * 4, SEEK_CUR );

        return mb_substr($data, 0, $length );
    }
}