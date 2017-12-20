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

use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Model\User;
use FOS\UserBundle\Services\EmailConfirmation\EmailEncryption;
use FOS\UserBundle\Services\EmailConfirmation\EmailUpdateConfirmation;
use FOS\UserBundle\Util\TokenGenerator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EmailUpdateConfirmationTest extends TestCase
{
    /** @var ExpressionFunctionProviderInterface */
    private $provider;
    /** @var RouterInterface */
    private $router;
    /** @var TokenGenerator */
    private $tokenGenerator;
    /** @var MailerInterface */
    private $mailer;
    /** @var EmailEncryption */
    private $emailEncryption;
    /** @var EventDispatcher */
    private $eventDispatcher;
    /** @var EmailUpdateConfirmation */
    private $emailUpdateConfirmation;
    /** @var User */
    private $user;
    private $cypher_method = 'AES-128-CBC';

    /** @var ValidatorInterface */
    private $emailValidator;
    /** @var ConstraintViolationList */
    private $constraintViolationList;

    protected function setUp()
    {
        $this->emailValidator = $this->getMockBuilder('Symfony\Component\Validator\Validator\RecursiveValidator')->disableOriginalConstructor()->getMock();
        $this->constraintViolationList = new ConstraintViolationList(array());
        $this->emailValidator->expects($this->once())->method('validate')->will($this->returnValue($this->constraintViolationList));

        $this->provider = $this->getMockBuilder('Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface')->getMock();
        $this->user = $this->getMockBuilder('FOS\UserBundle\Model\User')
                ->disableOriginalConstructor()
                ->getMock();
        $this->router = $this->getMockBuilder('Symfony\Bundle\FrameworkBundle\Routing\Router')
            ->disableOriginalConstructor()
            ->getMock();

        $this->tokenGenerator = $this->getMockBuilder('FOS\UserBundle\Util\TokenGenerator')->disableOriginalConstructor()->getMock();
        $this->mailer = $this->getMockBuilder('FOS\UserBundle\Mailer\TwigSwiftMailer')->disableOriginalConstructor()->getMock();
        $this->emailEncryption = new EmailEncryption($this->emailValidator, $this->cypher_method);
        $this->eventDispatcher = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcherInterface')->getMock();

        $this->emailUpdateConfirmation = new EmailUpdateConfirmation($this->router, $this->tokenGenerator, $this->mailer, $this->emailEncryption, $this->eventDispatcher);
        $this->user->expects($this->any())
            ->method('getConfirmationToken')
            ->will($this->returnValue('test_token'));
        $this->emailUpdateConfirmation->setUser($this->user);
    }

    public function testFetchEncryptedEmailFromConfirmationLinkMethod()
    {
        $emailEncryption = new EmailEncryption($this->emailValidator, $this->cypher_method);
        $emailEncryption->setEmail('foo@example.com');
        $emailEncryption->setUserConfirmationToken('test_token');

        $encryptedEmail = $emailEncryption->encryptEmailValue();

        $email = $this->emailUpdateConfirmation->fetchEncryptedEmailFromConfirmationLink($encryptedEmail);
        $this->assertSame('foo@example.com', $email);
    }
}
