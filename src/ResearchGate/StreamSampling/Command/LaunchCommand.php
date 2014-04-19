<?php

namespace ResearchGate\StreamSampling\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use ResearchGate\StreamSampling\Stream\StreamSampler;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Processor;
use ResearchGate\StreamSampling\Configuration\YmlConfigurationLoader;
use ResearchGate\StreamSampling\Configuration\Configuration;
use ResearchGate\StreamSampling\Helper\StringHelper;

/**
 * Console command to work with the stream sampler.
 */
class LaunchCommand extends Command
{
    private $lastMessagesLength = 0;

    private $configuration;

    private $stopwatch;

    protected function configure()
    {
        $this
        ->setName( 'sampler:sample' )
        ->setDescription( 'Stream sampler console application' );

        $this->loadConfiguration();

        $this->stopwatch = new Stopwatch();
    }


    protected function execute( InputInterface $input, OutputInterface $output )
    {
        $inputInfo  = $this->askInputDataKind( $output );
        $sampleSize = $this->askSampleSize( $output );

        $stream = StreamBuilder::getStream( $inputInfo );

        $sampler = new StreamSampler();
        $this->doBeforeWork( $sampler, $output );
        $sample = $sampler->getSample( $sampleSize, $stream );
        $this->doAfterWork( $sample, $output );


    }

    /**
     * @param OutputInterface $output
     *
     * @return array
     */
    private function askInputDataKind( OutputInterface $output )
    {
        $dialog             = $this->getHelperSet()->get( 'dialog' );
        $possibleInputKinds = array_values(
            array_map(
                function ( $item ) {
                    return $item['description'];
                },
                $this->configuration['inputs']
            )
        );

        $inputKindIndex = $dialog->select(
            $output,
            'Choose kind of input data: ',
            $possibleInputKinds,
            0
        );


        $inputKind = array_keys( $this->configuration['inputs'] )[$inputKindIndex];
        return $this->configuration['inputs'][$inputKind];
    }

    private function askSampleSize( OutputInterface $output )
    {
        $dialog = $this->getHelperSet()->get( 'dialog' );

        return (integer)$dialog->ask(
            $output,
            'Please enter the sample size (default is 5): ',
            5
        );
    }


    private function loadConfiguration()
    {
        $directories   = array( __DIR__ . '/../../../../config' );
        $locator       = new FileLocator( $directories );
        $loader        = new YmlConfigurationLoader( $locator );
        $configValues  = $loader->load( $locator->locate( 'sampler.yml' ) );
        $processor     = new Processor();
        $configuration = new Configuration();

        $this->configuration = $processor->processConfiguration(
            $configuration,
            $configValues
        );
    }

    private function doBeforeWork( StreamSampler $sampler, OutputInterface $output )
    {
        $this->stopwatch->start( 'Sampler' );
        $stopwatch = $this->stopwatch;

        $sampler->setOnProgress(
            function ( $result ) use ( $output, $stopwatch ) {
                $this->overwrite( $output, $result );
                $this->stopwatch->lap( 'Sampler' );
            }
        );

        $this->lastMessagesLength = 0;
    }

    /**
     * Outputs the report data
     *
     * @param                 $sample
     * @param OutputInterface $output
     */
    private function doAfterWork( $sample, OutputInterface $output )
    {
        $event = $this->stopwatch->stop( 'Sampler' );
        $output->writeln( '' );
        $output->writeln( '' );
        $output->writeln( 'Found sample: ' . $sample );
        $output->writeln( '' );
        $output->writeln( 'Report' );
        $table = $this->getHelperSet()->get( 'table' );
        $table
        ->setHeaders( array( 'Param', 'Value' ) )
        ->setRows(
                array(
                     array( 'Used memory', sprintf('%d MB', $event->getMemory() / 1024 ) ),
                     array( 'Duration', sprintf('%.1f min', $event->getDuration() / 1000 / 60 ) )
                )
            );
        $table->render( $output );
    }


    /**
     * Outputs progress information on the same place on the screen
     *
     * @param OutputInterface $output
     * @param string          $message
     */
    private function overwrite( OutputInterface $output, $message )
    {
        $length = StringHelper::length( $message, true );
        if (null !== $this->lastMessagesLength && $this->lastMessagesLength > $length) {
            $message = StringHelper::padString( $message, $this->lastMessagesLength, "\x20" );
        }
        $output->write( "\x0D" );
        $output->write( $message );

        $this->lastMessagesLength = StringHelper::length( $message, true );
    }

}