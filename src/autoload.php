<?php

require_once __DIR__ . '/../vendor/symfony/class-loader/Symfony/Component/ClassLoader/UniversalClassLoader.php';

$loader = new Symfony\Component\ClassLoader\UniversalClassLoader('ClassLoader');
$loader->registerNamespaces(array('ResearchGate' => __DIR__));
$loader->register();