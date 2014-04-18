<?php

namespace ResearchGate\StreamSampling\Stream;

use ResearchGate\StreamSampling\Input\InputInterface;
use ResearchGate\StreamSampling\Reader;
use ResearchGate\StreamSampling\Reader\StreamReaderInterface;

/**
 * Stream with possibility to read UTF8 symbols.
 */
class Stream
{
    /**
     * @var resource
     */
    private $handle;

    /**
     * Stream data reader
     * @var StreamReaderInterface
     */
    private $reader;

    /**
     * Stream data input
     * @var InputInterface
     */
    private $input;

    /**
     * @var bool
     */
    private $useUtf;

    public function __construct( InputInterface $input, $useUtf8 )
    {
        $this->handle = fopen( $input->getResourcePath(), 'rb' );
        $this->input  = $input;
        $this->useUtf = $useUtf8;

        $this->reader = $useUtf8 ? new Reader\UtfStreamReader() :
            new Reader\EasyStreamReader();

    }

    function __destruct()
    {
        if ($this->handle) {
            fclose( $this->handle );
        };
    }


    public function get( $length )
    {
        return $this->reader->get( $this->handle, $length );
    }

    public function restart()
    {
        // As soon as not stream wrappers allows to rewind stream, the stream is closed and opened again.
        if (!@rewind( $this->handle )) {
            fclose( $this->handle );
            $this->handle = fopen( $this->input->getResourcePath(), 'rb' );
        }
    }

    /**
     * Is end of the stream?
     * @return bool
     */
    public function isEnd()
    {
        return feof( $this->handle ) === true;
    }

    /**
     * @return bool
     */
    public function isUtf8()
    {
        return $this->useUtf;
    }

}