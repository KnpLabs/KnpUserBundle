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
            array('sha256', false, 1),
            array()
        );

        $userAccount = $this->getMock('Bundle\FOS\UserBundle\Model\UserInterface');

        $userAccount->expects($this->once())
            ->method('getAlgorithm')
            ->will($this->returnValue('sha512'));

        $encoder = $factory->getEncoder($userAccount);

        $expectedEncoder = new MessageDigestPasswordEncoder('sha512', false, 1);

        $this->assertEquals($expectedEncoder->encodePassword('foo', 'bar'), $encoder->encodePassword('foo', 'bar'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetEncoderWithGenericAccount()
    {
        $factory = new EncoderFactory(null, array(), array());

        $factory->getEncoder($this->getMock('Symfony\Component\Security\User\AccountInterface'));
    }
}
