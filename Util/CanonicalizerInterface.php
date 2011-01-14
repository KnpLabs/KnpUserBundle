<?php

namespace Bundle\FOS\UserBundle\Util;

interface CanonicalizerInterface
{
    function canonicalize($string);
}