<?php

namespace ResearchGate\StreamSampling\Command;

use ResearchGate\StreamSampling\Stream\Stream;
use ResearchGate\StreamSampling\Input;

/**
 * Builds stream using parameters from configuration file.
 */
class StreamBuilder
{
    /**
     * Builds a stream depending on configuration data.
     *
     * @param Array $inputInfo
     *
     * @return Stream
     */
    public static function getStream( $inputInfo )
    {
        $kind       = $inputInfo['kind'];
        $parameters = $inputInfo['parameters'];
        $useUtf8    = $inputInfo['use_utf8'];

        switch ($kind) {
            case 'Text':
                $input = new Input\TextInput( $parameters['text'] );
                break;
            case 'Random':
                $input = new Input\RandomInput( $parameters['read_count'] );
                break;
            case 'RandomOrg':
                $input = new Input\RandomOrgInput( $parameters['string_count'], $parameters['string_length'] );
                break;
            case 'File':
                $input = new Input\FileInput( __DIR__ . '/../../' . $parameters['path'] );
                break;
        }

        return new Stream( $input, $useUtf8 );

    }

}