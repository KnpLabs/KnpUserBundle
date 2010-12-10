<?php

/**
 * (c) Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bundle\DoctrineUserBundle\Security\Authentication\Provider;

use Bundle\DoctrineUserBundle\Security\Encoder\EncoderFactoryAwareInterface;
use Bundle\DoctrineUserBundle\Security\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\User\AccountInterface;
use Symfony\Component\Security\Authentication\Provider\DaoAuthenticationProvider as BaseDaoAuthenticationProvider;
use Symfony\Component\Security\Encoder\MessageDigestPasswordEncoder;
use Bundle\DoctrineUserBundle\Model\User;

class DaoAuthenticationProvider extends BaseDaoAuthenticationProvider implements EncoderFactoryAwareInterface
{
    protected $encoderFactory;
    
    public function setEncoderFactory(EncoderFactoryInterface $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
    }
    
    protected function checkAuthentication(AccountInterface $account, UsernamePasswordToken $token)
    {
        if (!$presentedPassword = (string) $token->getCredentials()) {
            throw new BadCredentialsException('Bad credentials');
        }

        if ($account instanceof User) {
            $passwordEncoder = $this->encoderFactory->getEncoder($account);
        }
        else {
            $passwordEncoder = $this->passwordEncoder;
        }
        
        if (!$passwordEncoder->isPasswordValid($account->getPassword(), $presentedPassword, $account->getSalt())) {
            throw new BadCredentialsException('Bad credentials');
        }
    }
}