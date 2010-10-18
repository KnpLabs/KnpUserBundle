<?php

namespace Bundle\DoctrineUserBundle\Tests\Validation;

use Bundle\DoctrineUserBundle\Test\WebTestCase;

class UserValidationTest extends WebTestCase
{
    public function testBlankUsernameFail()
    {
        $userClass = $this->getService('doctrine_user.user_repository')->getObjectClass();
        $user = new $userClass();
        $violations = $this->getService('validator')->validate($user);
        $this->assertTrue($this->hasViolationForPropertyPath($violations, 'username'));
    }

    public function testGoodUsernameValid()
    {
        $userClass = $this->getService('doctrine_user.user_repository')->getObjectClass();
        $user = new $userClass();
        $user->setUsername(uniqid());
        $violations = $this->getService('validator')->validate($user);
        $this->assertFalse($this->hasViolationForPropertyPath($violations, 'username'));
    }

    public function testDuplicatedUsernameFail()
    {
        $username = uniqid();
        $repo = $this->getService('doctrine_user.user_repository');
        $om = $repo->getObjectManager();
        $validator = $this->getService('validator');
        $userClass = $repo->getObjectClass();
        $user1 = new $userClass();
        $user1->setUsername($username);
        //$this->markTestSkipped();
        $violations = $this->getService('validator')->validate($user1);
        $this->assertFalse($this->hasViolationForPropertyPath($violations, 'username'));
        $om->persist($user1);
        $om->flush();
        $user2 = new $userClass();
        $user2->setUsername($username);
        $violations = $this->getService('validator')->validate($user2);
        $this->assertTrue($this->hasViolationForPropertyPath($violations, 'username'));
    }

    protected function hasViolationForPropertyPath($violations, $propertyPath)
    {
        if(!is_object($violations)) {
            return false;
        }

        foreach($violations as $violation) {
            if($violation->getPropertyPath() == $propertyPath) {
                return true;
            }
        }

        return false;
    }
}
