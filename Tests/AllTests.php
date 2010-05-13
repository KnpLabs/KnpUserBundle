<?php

namespace Bundle\DoctrineUserBundle\Tests;

require_once 'PHPUnit/Framework.php';
require_once __DIR__.'/Entities/UserTest.php';
require_once __DIR__.'/Entities/UserRepositoryTest.php';

class AllTests
{
  public static function suite()
  {
    $suite = new \PHPUnit_Framework_TestSuite('DoctrineUserBundle');

    $suite->addTestSuite('\Bundle\DoctrineUserBundle\Tests\Entities\UserTest');
    $suite->addTestSuite('\Bundle\DoctrineUserBundle\Tests\Entities\UserRepositoryTest');

    return $suite;
  }
}