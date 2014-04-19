<?php

namespace ResearchGate\StreamSampling\Reader;


class UtfStreamReader implements StreamReaderInterface {

    // http://en.wikipedia.org/wiki/UTF-8#Description
    const ONE_BYTE_LIMIT = 0x80;
    const TWO_BYTE_LIMIT = 0xE0;
    const THREE_BYTE_LIMIT = 0xF0;
    const FOUR_BYTE_LIMIT = 0xF8;


    private function fetchUtfSymbol( $handle )
    {
        $symbol     = fread( $handle, 1 );
        $asciiValue = ord( $symbol );
        if ($asciiValue < self::ONE_BYTE_LIMIT) {
        } elseif ($asciiValue < self::TWO_BYTE_LIMIT) {
            $symbol .= fread( $handle, 1 );
        } elseif ($asciiValue < self::THREE_BYTE_LIMIT) {
            $symbol .= fread( $handle, 2 );
        } elseif ($asciiValue < self::FOUR_BYTE_LIMIT) {
            $symbol .= fread( $handle, 3 );
        }
        return $symbol;
    }


    public function get( $handle, $length )
    {
        $portion = '';
        if ($length > 1) {
            for ($i = 1; $i <= $length; $i++) {
                $portion .= $this->fetchUtfSymbol( $handle );
                if(feof($handle)){
                    break;
                }
            }
        }

        return $portion;
    }
}