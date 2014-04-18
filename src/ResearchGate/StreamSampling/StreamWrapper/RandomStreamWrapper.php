<?php

namespace ResearchGate\StreamSampling\StreamWrapper;

class RandomStreamWrapper
{
    private $position;
    private $length;

    function stream_open( $path, $mode, $options, &$opened_path )
    {
        $this->position = 0;
        $this->length   = (int)str_replace( 'random://', '', $path );
        return true;
    }

    function stream_read( $count )
    {
        $base64 = base64_encode( openssl_random_pseudo_bytes( $count ) );
        $this->position += $count;
        return substr( $base64, 0, $count );
    }

    function stream_write( $data )
    {
        return false;
    }

    function stream_tell()
    {
        return $this->position;
    }

    function stream_eof()
    {
        return $this->position >= $this->length;
    }

    function stream_seek( $offset, $whence )
    {
        return false;
    }

    function stream_metadata( $path, $option, $var )
    {
        return false;
    }
}


?>