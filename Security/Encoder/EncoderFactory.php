<?php

/**
 * (c) Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bundle\FOS\UserBundle\Security\Encoder;

use Symfony\Component\Security\Encoder\EncoderFactory as BaseEncoderFactory;
use Symfony\Component\Security\User\AccountInterface;
use Bundle\FOS\UserBundle\Model\UserInterface;

/**
 * This factory assumes MessageDigestPasswordEncoder's constructor.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 * @author Jeremy Mikola <jmikola@gmail.com>
 */
class EncoderFactory extends BaseEncoderFactory
{
    protected $fosEncoders;
    protected $fosEncoderClass;
    protected $fosEncoderArgs;

    /**
     * Constructor.
     *
     * @param string $fosEncoderClass Encoder class
     * @param array  $fosEncoderArgs  Default encoder constructor arguments
     * @param array  $encoderMap      Encoder map for base EncoderFactory class
     */
    public function __construct($fosEncoderClass, array $fosEncoderArgs, array $encoderMap)
    {
        parent::__construct($encoderMap);

        $this->fosEncoderClass = $fosEncoderClass;
        $this->fosEncoderArgs = $fosEncoderArgs;
        $this->fosEncoders = array();
    }

    /**
     * @see Symfony\Component\Security\Encoder.EncoderFactory::getEncoder()
     */
    public function getEncoder(AccountInterface $account)
    {
        if (!$account instanceof UserInterface) {
            return parent::getEncoder($account);
        }

        $algorithm = $account->getAlgorithm();

        if (isset($this->fosEncoders[$algorithm])) {
            return $this->fosEncoders[$algorithm];
        }

        return $this->createFosEncoder($algorithm);
    }

    /**
     * Creates an encoder for the given algorithm.
     *
     * @param string $algorithm
     * @return \Symfony\Component\Security\Encoder\MessageDigestPasswordEncoder
     */
    protected function createFosEncoder($algorithm)
    {
        $arguments = $this->fosEncoderArgs;
        $arguments[0] = $algorithm;

        $reflection = new \ReflectionClass($this->fosEncoderClass);
        $this->encoders[$algorithm] = $reflection->newInstanceArgs($arguments);

        return $this->encoders[$algorithm];
    }
}