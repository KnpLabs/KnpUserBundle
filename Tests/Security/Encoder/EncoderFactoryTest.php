<?php

namespace Bundle\FOS\UserBundle\Tests\Security\Encoder;

use Symfony\Component\Security\Encoder\MessageDigestPasswordEncoder;
use Bundle\FOS\UserBundle\Security\Encoder\EncoderFactory;

class EncoderFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Bundle\FOS\UserBundle\Security\Encoder\EncoderFactory::getEncoder
     * @covers Bundle\FOS\UserBundle\Security\Encoder\EncoderFactory::createFosEncoder
     */
    public function testGetEncoderWithUserAccount()
    {
        $factory = new EncoderFactory(
            'Symfony\Component\Security\Encoder\MessageDigestPasswordEncoder',
            false,
            1,
            $this->getMock('Symfony\Component\Security\Encoder\EncoderFactoryInterface')
        );

        $userAccount = $this->getMock('Bundle\FOS\UserBundle\Model\UserInterface');

        $userAccount->expects($this->once())
            ->method('getAlgorithm')
            ->will($this->returnValue('sha512'));

        $encoder = $factory->getEncoder($userAccount);

        $expectedEncoder = new MessageDigestPasswordEncoder('sha512', false, 1);

        $this->assertEquals($expectedEncoder->encodePassword('foo', 'bar'), $encoder->encodePassword('foo', 'bar'));
    }

    public function testGetEncoderWithGenericAccount()
    {
        $genericFactory = $this->getMock('Symfony\Component\Security\Encoder\EncoderFactoryInterface');
        $encoder = $this->getMock('Symfony\Component\Security\Encoder\PasswordEncoderInterface');

        $genericFactory
            ->expects($this->once())
            ->method('getEncoder')
            ->will($this->returnValue($encoder))
        ;

        $factory = new EncoderFactory(null , false, 1, $genericFactory);

        $this->assertSame($encoder, $factory->getEncoder($this->getMock('Symfony\Component\Security\User\AccountInterface')));
    }
}
