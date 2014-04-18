#!/usr/bin/env php
<?php

use ResearchGate\StreamSampling\Command\LaunchCommand;
use Symfony\Component\Console\Application;

require __DIR__ . '/../vendor/autoload.php';

$application = new Application();
$application->add(new LaunchCommand);
$application->run();