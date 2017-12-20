<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Tests\Util;

use FOS\UserBundle\Services\EmailConfirmation\EmailEncryption;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EmailEncryptionTest extends TestCase
{
    /** @var ValidatorInterface */
    private $emailValidator;
    /** @var ConstraintViolationList */
    private $constraintViolationList;

    protected function setUp()
    {
        $this->emailValidator = $this->getMockBuilder('Symfony\Component\Validator\Validator\RecursiveValidator')->disableOriginalConstructor()->getMock();
        $this->constraintViolationList = new ConstraintViolationList(array($this->getMockBuilder('Symfony\Component\Validator\ConstraintViolation')->disableOriginalConstructor()->getMock()));
    }

    public function testEncryptDecryptEmail()
    {
        $this->emailValidator->expects($this->once())->method('validate')->will($this->returnValue($this->constraintViolationList));
        $this->constraintViolationList->remove(0);
        $emailEncryption = new EmailEncryption($this->emailValidator);
        $emailEncryption->setEmail('foo@example.com');
        $emailEncryption->setUserConfirmationToken('test_token');

        $encryptedEmail = $emailEncryption->encryptEmailValue();
        $this->assertSame('foo@example.com', $emailEncryption->decryptEmailValue($encryptedEmail));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testDecryptFromWrongEmailFormat()
    {
        $this->emailValidator->expects($this->once())->method('validate')->will($this->returnValue($this->constraintViolationList));
        $emailEncryption = new EmailEncryption($this->emailValidator);
        $emailEncryption->setEmail('fooexample.com');
        $emailEncryption->setUserConfirmationToken('test_token');

        $encryptedEmail = $emailEncryption->encryptEmailValue();
        $emailEncryption->decryptEmailValue($encryptedEmail);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testIntegerIsSetInsteadOfEmailString()
    {
        $emailEncryption = new EmailEncryption($this->emailValidator);
        $emailEncryption->setEmail(123);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testIntegerIsSetInsteadOfConfirmationTokenString()
    {
        $emailEncryption = new EmailEncryption($this->emailValidator);
        $emailEncryption->setUserConfirmationToken(123);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNullIsSetInsteadOfConfirmationTokenString()
    {
        $emailEncryption = new EmailEncryption($this->emailValidator);
        $emailEncryption->setUserConfirmationToken(null);
    }

    public function testGetConfirmationToken()
    {
        $this->constraintViolationList->remove(0);
        $emailEncryption = new EmailEncryption($this->emailValidator);
        $emailEncryption->setUserConfirmationToken('test_token');

        $confirmationToken = $emailEncryption->getConfirmationToken();
        $expectedConfirmationToken = pack('H*', hash('sha256', 'test_token'));
        $this->assertSame($expectedConfirmationToken, $confirmationToken);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetConfirmationTokenIfUserConfirmationTokenIsNotSet()
    {
        $emailEncryption = new EmailEncryption($this->emailValidator);
        $emailEncryption->getConfirmationToken();
    }
}
