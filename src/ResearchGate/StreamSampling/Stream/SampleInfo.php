<?php

namespace ResearchGate\StreamSampling\Stream;

/**
 * Data structure that gives information about the sample.
 */
class SampleInfo
{

    /**
     * @param $sampleData
     * @return string
     */
    public static function getText( $sampleData )
    {
        return implode( '', $sampleData );
    }

    /**
     * @param $sampleData
     * @param $reserveData
     * @param $length
     * @return string
     */
    public static function getFullDescription( $sampleData, $reserveData, $length )
    {
        return sprintf(
            "Iteration: %.0f | Sample: %s | Symbol positions: %s | Reserve size: %d",
            $length,
            implode( '', $sampleData ),
            implode( ' ', array_keys( $sampleData ) ),
            count( $reserveData )
        );
    }

    /**
     * Fins a symbol in the sample data.
     * @param $sampleData
     * @param $start
     * @param $finish
     *
     * @return bool|int|string
     */
    public static function getSymbolPosition( $sampleData, $start, $finish )
    {
        foreach ($sampleData as $position => $sampleItem) {
            if ($start <= $position && $position < $finish) {
                return $position;
            }
        }
        return false;
    }


}