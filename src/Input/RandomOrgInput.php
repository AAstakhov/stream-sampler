<?php

namespace ResearchGate\StreamSampling\Input;

class RandomOrgInput implements InputInterface
{
    private $stringCount;
    private $stringLength;

    const STRING_GENERATOR_API_URL = 'http://www.random.org/strings/';

    /**
     * @param int $stringCount
     * @param int $stringLength
     */
    public function __construct( $stringCount, $stringLength )
    {
        $this->stringCount  = $stringCount;
        $this->stringLength = $stringLength;
    }


    public function getResourcePath()
    {
        $parameters = [
            'num'        => $this->stringCount,
            'len'        => $this->stringLength,
            'digits'     => 'on',
            'upperalpha' => 'on',
            'loweralpha' => 'on',
            'unique'     => 'on',
            'format'     => 'plain',
            'rnd'        => 'new'
        ];

        $queryString = implode(
            '&',
            array_map(

                function ( $key ) use ( $parameters ) {
                    return sprintf( '%s=%s', $key, $parameters[$key] );
                },
                array_keys( $parameters )

            )
        );
        // Build url like http://www.random.org/strings/?num=1000&len=20&digits=on&upperalpha=on&loweralpha=on&unique=on&format=plain&rnd=new
        $url = self::STRING_GENERATOR_API_URL . '?' . $queryString;
        return $url;
    }
}