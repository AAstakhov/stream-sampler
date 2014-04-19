<?php

namespace ResearchGate\StreamSampling\Stream;

use ResearchGate\StreamSampling\Helper\StringHelper;

/**
 * Finds a random (representative) sample  from a stream of values with unknown and possibly very large length.
 */
class StreamSampler
{

    /**
     * Current length of data was read from a stream.
     * @var int
     */
    private $length;

    /**
     * Sample symbols that are calculated on a current step of reading a new portion from a stream.
     * @var array
     *
     * Array keys are positions of symbols in a stream, array values are symbols from a stream.
     */
    private $sampleSymbols = [ ];

    /**
     * Current reserve symbols which are needed for recalculation a sample on each reading from a stream.
     * @var array
     */
    private $reserveSymbols = [ ];

    /**
     * Simple callback event to notify about stream reading progress.
     * @var Closure
     */
    private $onProgress;

    /**
     * Whether to read data as UTF8 symbols from the stream.
     * When set to true, works significantly slower but gives real UTF8 symbols.
     * @var bool
     */
    private $useUtf = false;

    /**
     * Shows that stream has to be reread after the first stage of picking sample when some symbols were lost.
     * @var bool
     */
    private $hasToRereadStream = false;

    /**
     * Data portion to be read on the first fetch.
     *
     * Both FIRST_FETCH_SIZE and FETCH_SIZE are definitely big in order to work with long and short streams.
     */
    const FIRST_FETCH_SIZE = 500000;
    /**
     * Data portion to be read on each fetch.
     */
    const FETCH_SIZE       = 100000;

    const PROGRESS_NOTIFICATION_INTERVAL = 100;


    /**
     * Picks $size symbols from the stream.
     *
     * @param int    $size
     * @param Stream $stream
     *
     * @return string
     */
    public function getSample( $size, Stream $stream )
    {
        $this->doBeforeGetSample( $stream );
        $iteration = 0;

        // Stage 1: Read data from stream calculating new sample on each reading
        do {

            $fetchSize = $iteration == 0 ? self::FIRST_FETCH_SIZE : self::FETCH_SIZE;

            $portion       = $stream->get( $fetchSize );
            // Calculate new sample using new portion of data
            $newSampleSymbols = $this->getNewSampleSymbols( $size, $this->length, $portion );

            // Save sample symbols from the previous sample
            $replaceableSymbols  = $this->getReplaceableSampleSymbols( $this->sampleSymbols, $newSampleSymbols );
            $this->reserveSymbols = array_merge( $this->reserveSymbols, $replaceableSymbols );

            $this->sampleSymbols = $newSampleSymbols;
            $this->length = $this->length + StringHelper::length( $portion, $this->useUtf );

            // Notify about reading progress
            $this->doOnProgress( $iteration++ );

        } while (!$stream->isEnd());

        // Stage 2: If some symbols are lost, reread them from the stream.
        // This stage is performed in 5% of cases when input stream is very large.
        if ($this->hasToRereadStream) {
            $this->rereadStreamToFindLostSymbols( $stream );
        }

        return SampleInfo::getText( $this->sampleSymbols );
    }

    /**
     * Finds new sample symbols for new portion of data from the stream.
     *
     * @param integer $size
     * @param float $oldLength
     * @param float $newPortion
     *
     * @return array
     */
    protected function getNewSampleSymbols( $size, $oldLength, $newPortion )
    {
        $newLength      = (float)( $oldLength + StringHelper::length( $newPortion, $this->useUtf ) );
        $intervalLength = (float)( $newLength / $size );

        $newSampleData = [ ];

        for ($i = 0; $i < $size; $i++) {

            $intervalStart  = (float)$i * $intervalLength;
            $intervalFinish = (float)$intervalStart + $intervalLength;

            $symbolPosition = false;
            if (!empty( $this->sampleSymbols )) {
                $symbolPosition = SampleInfo::getSymbolPosition( $this->sampleSymbols, $intervalStart, $intervalFinish );
            }

            if ($symbolPosition) {
                // If sample symbol is found, leave it in the sample.
                $newSampleData[$symbolPosition] = $this->sampleSymbols[$symbolPosition];
            } else {

                // Border situation: old stream length lays in the new interval
                if ($this->isNumberInInterval( $oldLength, $intervalStart, $intervalFinish )) {
                    $symbol                                 = StringHelper::getRandomSymbolInText(
                        $newPortion,
                        0,
                        $intervalFinish - $oldLength,
                        $this->useUtf
                    );
                    $newSampleData[$symbol[1] + $oldLength] = $symbol[0];
                } elseif ($intervalStart > $oldLength) {
                    // Old length lays before new interval
                    $symbol                                 = StringHelper::getRandomSymbolInText(
                        $newPortion,
                        $intervalStart - $oldLength,
                        $intervalFinish - $oldLength,
                        $this->useUtf
                    );
                    $newSampleData[$symbol[1] + $oldLength] = $symbol[0];
                } else {
                    // Symbol is lost. Try to find it in reserve symbol list.
                    $symbolPosition = SampleInfo::getSymbolPosition( $this->reserveSymbols, $intervalStart, $intervalFinish );
                    if ($symbolPosition) {
                        $newSampleData[$symbolPosition] = $this->reserveSymbols[$symbolPosition];
                    } else {
                        // Symbol is utterly lost. Set flah to reread stream on the second stage.
                        $newSampleData[$intervalStart] = '';
                        $this->hasToRereadStream       = true;
                    }
                }
            }
        }

        return $newSampleData;
    }

    /**
     * Reread stream to find lost symbols
     * @param Stream $stream
     * @param        $item
     *
     * @return string
     */
    protected function rereadStreamToFindLostSymbols( Stream $stream )
    {
        $stream->restart();
        $length = 0;
        foreach ($this->sampleSymbols as $position => &$item) {
            if ($item === '') {
                $stream->get( $position - $length - 1 );
                $item = $stream->get( 1 );
                $length += $position;
            }
        }
    }

    /**
     * Compares to symbol arrays and finds which symbols are gone from the sample.
     * @param array $oldData
     * @param array $newData
     *
     * @return array
     */
    protected function getReplaceableSampleSymbols( $oldData, $newData )
    {
        return array_diff_key( $oldData, $newData );
    }


    private function isNumberInInterval( $number, $start, $finish )
    {
        return $start <= $number && $number < $finish;
    }


    /**
     * Initialization before reading the stream
     * @param Stream $stream
     */
    private function doBeforeGetSample( Stream $stream )
    {
        $this->useUtf = $stream->isUtf8();
        $this->length = 0.0;
        settype( $this->length, 'float' );
        $this->hasToRereadStream = false;
    }


    public function setOnProgress( \Closure $onProgress )
    {
        $this->onProgress = $onProgress;
    }

    protected function doOnProgress( $iteration )
    {
        if ($iteration % self::PROGRESS_NOTIFICATION_INTERVAL == 1) {
            if ($this->onProgress) {
                call_user_func_array(
                    $this->onProgress,
                    [ SampleInfo::getFullDescription( $this->sampleSymbols, $this->reserveSymbols, $this->length ) ]
                );
            }
        }
    }
}