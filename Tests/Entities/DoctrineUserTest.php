<?php

namespace Bundle\DoctrineUserBundle\Tests\Entities;

use Bundle\DoctrineUserBundle\Entities\DoctrineUser;

require_once 'PHPUnit/Framework.php';
require_once __DIR__.'/../../Entities/DoctrineUser.php';

class DoctrineUserTest extends \PHPUnit_Framework_TestCase
{
  public function testDummy()
  {
    $user = new DoctrineUser();
    $this->assertTrue($user->getCreatedAt() instanceof \DateTime);
  }
  
}