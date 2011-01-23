<?php

namespace FOS\UserBundle\Util;

class Canonicalizer implements CanonicalizerInterface
{
    public function canonicalize($string)
    {
        return mb_convert_case($string, MB_CASE_LOWER, mb_detect_encoding($string));
    }
}
