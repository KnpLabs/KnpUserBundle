<?php

/**
 * (c) Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bundle\FOS\UserBundle\Security\Authentication\Provider;

use Bundle\FOS\UserBundle\Model\User;
use Bundle\FOS\UserBundle\Security\Encoder\EncoderFactoryAwareInterface;
use Bundle\FOS\UserBundle\Security\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\User\AccountInterface;
use Symfony\Component\Security\Authentication\Provider\DaoAuthenticationProvider as BaseDaoAuthenticationProvider;
use Symfony\Component\Security\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Exception\BadCredentialsException;

class DaoAuthenticationProvider extends BaseDaoAuthenticationProvider implements EncoderFactoryAwareInterface
{
    protected $encoderFactory;

    public function setEncoderFactory(EncoderFactoryInterface $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
    }

    protected function checkAuthentication(AccountInterface $account, UsernamePasswordToken $token)
    {
        $user = $token->getUser();
        if ($user instanceof AccountInterface) {
            if ($account->getPassword() !== $user->getPassword()) {
                throw new BadCredentialsException('The credentials were changed from another session.');
            }
        } else {
            if (!$presentedPassword = (string) $token->getCredentials()) {
                throw new BadCredentialsException('Bad credentials');
            }

            if ($account instanceof User) {
                $passwordEncoder = $this->encoderFactory->getEncoder($account);
            } else {
                $passwordEncoder = $this->passwordEncoder;
            }

            if (!$passwordEncoder->isPasswordValid($account->getPassword(), $presentedPassword, $account->getSalt())) {
                throw new BadCredentialsException('Bad credentials');
            }
        }
    }
}
