<?php

namespace ResearchGate\StreamSampling\Input;

class FileInput implements InputInterface
{
    private $path;

    public function __construct( $path )
    {
        $this->path = $path;
    }


    public function getResourcePath()
    {
        return $this->path;
    }
}