<?php

namespace ResearchGate\StreamSampling\Helper;

/**
 * String functions with a support of UTF8.
 */
class StringHelper
{

    public static function length( $string, $useUtf )
    {
        if ($useUtf) {
            return mb_strlen( $string, 'utf-8' );
        } else {
            return strlen( $string );
        }
    }

    public static function substring( $string, $start, $length, $useUtf )
    {
        if ($useUtf) {
            return mb_substr( $string, $start, $length, 'utf-8' );
        } else {
            return substr( $string, $start, $length );
        }
    }

    public static function getRandomSymbolInText( $text, $start, $finish, $useUtf )
    {
        $randomIndex = mt_rand( $start, $finish - 1 );
        return [
            0 => self::substring( $text, $randomIndex, 1, $useUtf ),
            1 => $randomIndex
        ];
    }

    public function padString( $input, $pad_length, $pad_string = ' ', $pad_type = STR_PAD_RIGHT )
    {
        $diff = strlen( $input ) - mb_strlen( $input, 'utf-8' );
        return str_pad( $input, $pad_length + $diff, $pad_string, $pad_type );
    }


}