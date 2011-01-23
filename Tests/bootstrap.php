<?php

require_once $_SERVER['SYMFONY'].'/Symfony/Component/HttpFoundation/UniversalClassLoader.php';

use Symfony\Component\HttpFoundation\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespace('Symfony', $_SERVER['SYMFONY']);
$loader->register();

spl_autoload_register(function($class)
{
    if (0 === strpos($class, 'FOS\\UserBundle\\')) {
        $path = implode('/', array_slice(explode('\\', $class), 2)).'.php';
        require_once __DIR__.'/../'.$path;
        return true;
    }
});

call_user_func(array('FOS\\UserBundle\\Entity\\User', 'setCanonicalizer'), new FOS\UserBundle\Util\Canonicalizer());
call_user_func(array('FOS\\UserBundle\\Document\\User', 'setCanonicalizer'), new FOS\UserBundle\Util\Canonicalizer());
