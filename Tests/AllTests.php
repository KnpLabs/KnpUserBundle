<?php

namespace Bundle\DoctrineUserBundle\Tests;

require_once 'PHPUnit/Framework.php';
require_once __DIR__.'/Entities/DoctrineUserTest.php';

class AllTests
{
  public static function suite()
  {
    $suite = new \PHPUnit_Framework_TestSuite('DoctrineUserBundle');

    $suite->addTestSuite('\Bundle\DoctrineUserBundle\Tests\Entities\DoctrineUserTest');

    return $suite;
  }
}