<?php

namespace ResearchGate\StreamSampling\StreamWrapper;

/**
 * Custom stream wrapper to generate random data.
 */
class RandomStreamWrapper
{
    private $position;
    private $length;

    const PROTOCOL = 'random';

    function stream_open( $path, $mode, $options, &$opened_path )
    {
        $this->position = 0;
        $this->length   = (int)str_replace( self::PROTOCOL . '://', '', $path );
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