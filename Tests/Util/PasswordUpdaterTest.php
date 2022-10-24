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

use FOS\UserBundle\Tests\TestUser;
use FOS\UserBundle\Util\PasswordUpdater;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

/**
 * @group legacy
 */
class PasswordUpdaterTest extends TestCase
{
    /**
     * @var PasswordUpdater&MockObject
     */
    private $updater;
    private $encoderFactory;

    protected function setUp(): void
    {
        if (!interface_exists(EncoderFactoryInterface::class)) {
            $this->markTestSkipped('The PasswordUpdater class does not support Symfony 6+.');
        }

        $this->encoderFactory = $this->getMockBuilder(EncoderFactoryInterface::class)->getMock();

        $this->updater = new PasswordUpdater($this->encoderFactory);
    }

    public function testUpdatePassword()
    {
        $encoder = $this->getMockPasswordEncoder();
        $user = new TestUser();
        $user->setPlainPassword('password');

        $this->encoderFactory->expects($this->once())
            ->method('getEncoder')
            ->with($user)
            ->will($this->returnValue($encoder));

        $encoder->expects($this->once())
            ->method('encodePassword')
            ->with('password', $this->isType('string'))
            ->will($this->returnValue('encodedPassword'));

        $this->updater->hashPassword($user);
        $this->assertSame('encodedPassword', $user->getPassword(), '->updatePassword() sets encoded password');
        $this->assertNotNull($user->getSalt());
        $this->assertNull($user->getPlainPassword(), '->updatePassword() erases credentials');
    }

    public function testUpdatePasswordWithBCrypt()
    {
        if (!class_exists('Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder')) {
            $this->markTestSkipped('This test requires Symfony 4');
        }

        $encoder = $this->getMockBuilder('Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder')
            ->disableOriginalConstructor()
            ->getMock();
        $user = new TestUser();
        $user->setPlainPassword('password');
        $user->setSalt('old_salt');

        $this->encoderFactory->expects($this->once())
            ->method('getEncoder')
            ->with($user)
            ->will($this->returnValue($encoder));

        $encoder->expects($this->once())
            ->method('encodePassword')
            ->with('password', $this->isNull())
            ->will($this->returnValue('encodedPassword'));

        $this->updater->hashPassword($user);
        $this->assertSame('encodedPassword', $user->getPassword(), '->updatePassword() sets encoded password');
        $this->assertNull($user->getSalt());
        $this->assertNull($user->getPlainPassword(), '->updatePassword() erases credentials');
    }

    public function testDoesNotUpdateWithEmptyPlainPassword()
    {
        $user = new TestUser();
        $user->setPassword('hash');

        $user->setPlainPassword('');

        $this->updater->hashPassword($user);
        $this->assertSame('hash', $user->getPassword());
    }

    public function testDoesNotUpdateWithoutPlainPassword()
    {
        $user = new TestUser();
        $user->setPassword('hash');

        $user->setPlainPassword(null);

        $this->updater->hashPassword($user);
        $this->assertSame('hash', $user->getPassword());
    }

    private function getMockPasswordEncoder()
    {
        return $this->getMockBuilder('Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface')->getMock();
    }
}
