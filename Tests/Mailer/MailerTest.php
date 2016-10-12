<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Tests\Mailer;

use FOS\UserBundle\Command\ActivateUserCommand;
use FOS\UserBundle\Mailer\Mailer;
use FOS\UserBundle\Model\UserInterface;
use Swift_Events_EventDispatcher;
use Swift_Mailer;
use Swift_Transport_NullTransport;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MailerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider goodEmailProvider
     */
    public function testSendConfirmationEmailMessageWithGoodEmails($emailAddress)
    {
        $mailer = $this->getMailer();
        $mailer->sendConfirmationEmailMessage($this->getUser($emailAddress));

        $this->assertTrue(true);
    }

    /**
     * @dataProvider badEmailProvider
     * @expectedException Swift_RfcComplianceException
     */
    public function testSendConfirmationEmailMessageWithBadEmails($emailAddress)
    {
        $mailer = $this->getMailer();
        $mailer->sendConfirmationEmailMessage($this->getUser($emailAddress));
    }

    /**
     * @dataProvider goodEmailProvider
     */
    public function testSendResettingEmailMessageWithGoodEmails($emailAddress)
    {
        $mailer = $this->getMailer();
        $mailer->sendResettingEmailMessage($this->getUser($emailAddress));

        $this->assertTrue(true);
    }

    /**
     * @dataProvider badEmailProvider
     * @expectedException Swift_RfcComplianceException
     */
    public function testSendResettingEmailMessageWithBadEmails($emailAddress)
    {
        $mailer = $this->getMailer();
        $mailer->sendResettingEmailMessage($this->getUser($emailAddress));
    }

    private function getMailer()
    {
        return new Mailer(
            new Swift_Mailer(
                new Swift_Transport_NullTransport(
                    $this->getMock('Swift_Events_EventDispatcher')
                )
            ),
            $this->getMock('Symfony\Component\Routing\Generator\UrlGeneratorInterface'),
            $this->getTemplating(),
            array(
                'confirmation.template' => 'foo',
                'resetting.template' => 'foo',
                'from_email' => array(
                    'confirmation' => 'foo@example.com',
                    'resetting' => 'foo@example.com',
                ),
            )
        );
    }

    private function getTemplating()
    {
        $templating = $this->getMockBuilder('Symfony\Bundle\FrameworkBundle\Templating\EngineInterface')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        return $templating;
    }

    private function getUser($emailAddress)
    {
        $user = $this->getMock('FOS\UserBundle\Model\UserInterface');
        $user->method('getEmail')
            ->willReturn($emailAddress)
        ;

        return $user;
    }

    private function getEmailAddressValueObject($emailAddressAsString)
    {
        $emailAddress = $this->getMock('EmailAddress', array(
            '__toString',
        ));

        $emailAddress->method('__toString')
            ->willReturn($emailAddressAsString)
        ;

        return $emailAddress;
    }

    public function goodEmailProvider()
    {
        return array(
            array('foo@example.com'),
            array('foo@example.co.uk'),
            array($this->getEmailAddressValueObject('foo@example.com')),
            array($this->getEmailAddressValueObject('foo@example.co.uk')),
        );
    }

    public function badEmailProvider()
    {
        return array(
            array('foo'),
            array($this->getEmailAddressValueObject('foo')),
        );
    }
}
