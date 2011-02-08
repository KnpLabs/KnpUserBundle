<?php

namespace FOS\UserBundle\Tests\Validation;

use FOS\UserBundle\Test\WebTestCase;

class UserValidationTest extends WebTestCase
{
    public function testBlankUsernameFail()
    {
        $user = $this->getService('fos_user.user_manager')->createUser();
        $violations = $this->getService('validator')->validate($user, 'Registration');
        $this->assertTrue($this->hasViolationForPropertyPath($violations, 'username'));
    }

    public function testGoodUsernameValid()
    {
        $user = $this->getService('fos_user.user_manager')->createUser();
        $user->setUsername(uniqid());
        $violations = $this->getService('validator')->validate($user, 'Registration');
        $this->assertFalse($this->hasViolationForPropertyPath($violations, 'username'));
    }

    public function testDuplicatedUsernameFail()
    {
        $username = uniqid();
        $userManager = $this->getService('fos_user.user_manager');
        $user1 = $userManager->createUser();
        $user1->setUsername($username);
        $user1->setEmail(uniqid().'@mail.org');
        $user1->setPlainPassword(uniqid());
        $violations = $this->getService('validator')->validate($user1, 'Registration');
        $this->assertFalse($this->hasViolationForPropertyPath($violations, 'username'));
        $userManager->updateUser($user1);

        $user2 = $userManager->createUser();
        $user2->setUsername($username);
        $user1->setEmail(uniqid().'@mail.org');
        $user1->setPlainPassword(uniqid());
        $violations = $this->getService('validator')->validate($user2, 'Registration');
        $this->assertTrue($this->hasViolationForPropertyPath($violations, 'username'));
        $userManager->deleteUser($user1);
    }

    public function testDuplicatedEmailFail()
    {
        $email = uniqid().'@email.org';
        $userManager = $this->getService('fos_user.user_manager');
        $user1 = $userManager->createUser();
        $user1->setUsername(uniqid());
        $user1->setPlainPassword(uniqid());
        $user1->setEmail($email);
        $violations = $this->getService('validator')->validate($user1, 'Registration');
        $this->assertFalse($this->hasViolationForPropertyPath($violations, 'email'));
        $userManager->updateUser($user1);

        $user2 = $userManager->createUser();
        $user2->setUsername(uniqid());
        $user2->setPlainPassword(uniqid());
        $user2->setEmail($email);
        $violations = $this->getService('validator')->validate($user2, 'Registration');
        $this->assertTrue($this->hasViolationForPropertyPath($violations, 'email'));
        $userManager->deleteUser($user1);
    }

    protected function hasViolationForPropertyPath($violations, $propertyPath)
    {
        if (!is_object($violations)) {
            return false;
        }

        foreach ($violations as $violation) {
            if ($violation->getPropertyPath() == $propertyPath) {
                return true;
            }
        }

        return false;
    }
}
