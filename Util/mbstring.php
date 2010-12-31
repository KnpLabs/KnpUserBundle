<?php

function mb_strtolower($str, $encoding = 'UTF-8')
{
    return strtolower($str);
}

function mb_detect_encoding()
{
    return 'UTF-8';
}
