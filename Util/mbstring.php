<?php

if (!function_exists('mb_strtolower')) {
    function mb_strtolower($str, $encoding = 'UTF-8')
    {
        return strtolower($str);
    }
}

if (!function_exists('mb_detect_encoding')) {
    function mb_detect_encoding()
    {
        return 'UTF-8';
    }
}
