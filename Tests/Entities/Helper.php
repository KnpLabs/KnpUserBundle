<?php

namespace Bundle\DoctrineUserBundle\Tests\Entities;

use Doctrine\Common as Common;
use Doctrine\ORM as ORM;

class Helper
{

    /**
     * @return ORM\EntityManager
     */
    public function createEntityManager()
    {
        $config = new ORM\Configuration();
        $cache = new Common\Cache\ArrayCache();
        $annotationReader = new Common\Annotations\AnnotationReader($cache);
        $annotationReader->setDefaultAnnotationNamespace('Doctrine\\ORM\\Mapping\\');
        $annotationPath = __DIR__ . '/../../Entities';
        $annotationDriver = new ORM\Mapping\Driver\AnnotationDriver($annotationReader, array($annotationPath));
        $config->setMetadataDriverImpl($annotationDriver);
        $config->setMetadataCacheImpl($cache);
        $config->setQueryCacheImpl($cache);
        $config->setProxyDir(__DIR__ . '/../../Proxies');
        $config->setProxyNamespace('Bundle\\DoctrineUserBundle\\Proxies');

        $eventManager = new Common\EventManager();
        $eventManager->addEventListener(array("preTestSetUp"), $this);

        $conn = array(
            'driver' => 'pdo_sqlite',
            'memory' => true,
        );

        return ORM\EntityManager::create($conn, $config, $eventManager);
    }

    public function dropAndCreate(ORM\EntityManager $em, array $classes)
    {
        foreach($classes as $index => $class)
        {
            $classes[$index] = $em->getClassMetadata($class);
        }

        $schemaTool = new ORM\Tools\SchemaTool($em);
        $schemaTool->dropSchema($classes);
        $schemaTool->createSchema($classes);
    }

}

// Fire Doctrine autoload

if (!isset($GLOBALS['doctrine2-path'])) {
    throw new \InvalidArgumentException('Global variable "doctrine2-path" has to be set in phpunit.xml');
}

$loaderfile = $GLOBALS['doctrine2-path'] . "/Doctrine/Common/ClassLoader.php";
if (!file_exists($loaderfile)) {
    throw new \InvalidArgumentException(sprintf(
        'Could not include %s',
        $loaderfile
    ));
}
require_once($loaderfile);

$loader = new Common\ClassLoader("Doctrine", $GLOBALS['doctrine2-path']);
$loader->register();
