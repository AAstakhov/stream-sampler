<?php

namespace ResearchGate\StreamSampling\Configuration;

use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Yaml\Yaml;

class YmlConfigurationLoader extends FileLoader
{
    public function load( $resource, $type = null )
    {
        $configValues = Yaml::parse( $resource );
        return $configValues;
    }

    public function supports( $resource, $type = null )
    {
        return is_string( $resource ) && 'yml' === pathinfo(
            $resource,
            PATHINFO_EXTENSION
        );
    }
}
