<?php

namespace Bundle\DoctrineUserBundle\Security\Encoder;

interface EncoderFactoryAwareInterface
{
    function setEncoderFactory(EncoderFactoryInterface $factory);
}