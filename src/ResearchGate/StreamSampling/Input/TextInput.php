<?php

namespace ResearchGate\StreamSampling\Input;

class TextInput implements InputInterface
{
    /**
     * @var string
     */
    private $text;

    public function __construct( $text )
    {
        $this->text = $text;
    }

    public function getResourcePath()
    {
        return 'data://text/plain,' . $this->text;
    }
}