<?php

namespace Bundle\DoctrineUserBundle\Security\Authentication\Provider;

use Symfony\Component\Security\Authentication\Provider\DaoAuthenticationProvider as BaseDaoAuthenticationProvider;
use Symfony\Component\Security\Encoder\MessageDigestPasswordEncoder;
use Bundle\DoctrineUserBundle\Model\User;

class DaoAuthenticationProvider extends BaseDaoAuthenticationProvider
{
    protected static $encoders = array();
    
    protected function checkAuthentication(AccountInterface $account, UsernamePasswordToken $token)
    {
        if (!$presentedPassword = (string) $token->getCredentials()) {
            throw new BadCredentialsException('Bad credentials');
        }

        if ($account instanceof User) {
            $passwordEncoder = $this->getPasswordEncoder($account);
        }
        else {
            $passwordEncoder = $this->passwordEncoder;
        }
        
        if (!$passwordEncoder->isPasswordValid($account->getPassword(), $presentedPassword, $account->getSalt())) {
            throw new BadCredentialsException('Bad credentials');
        }
    }
    
    protected function getPasswordEncoder(User $user)
    {
        if (!isset(self::$encoders[$algorithm = $user->getAlgorithm()])) {
            self::$encoders[$algorithm] = new MessageDigestPasswordEncoder($algorithm, false, 2);
        }
        
        return self::$encoders[$algorithm];
    }
}