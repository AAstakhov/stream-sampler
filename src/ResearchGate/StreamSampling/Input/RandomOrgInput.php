<?php

namespace ResearchGate\StreamSampling\Input;

class RandomOrgInput implements InputInterface
{
    public function __construct()
    {
    }


    public function getResourcePath()
    {
        return 'http://www.random.org/strings/?num=1000&len=20&digits=on&upperalpha=on&loweralpha=on&unique=on&format=plain&rnd=new';
    }
}