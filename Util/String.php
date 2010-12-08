<?php

namespace Bundle\DoctrineUserBundle\Util;

class String
{
    public static function strtolower($str)
    {    
        static $function = null;
        if (null === $function) {
            $function = extension_loaded('mb_string') ? 'mb_strtolower' : 'strtolower';
        }
        
        return $function($str);
    }
    
    private final function __construct() {}
}