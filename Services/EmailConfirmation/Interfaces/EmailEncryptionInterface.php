<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Services\EmailConfirmation\Interfaces;

/**
 * Interface EmailEncryptionInterface.
 */
interface EmailEncryptionInterface
{
    /**
     * @return string Encrypted email value
     */
    public function encryptEmailValue();

    /**
     * @param string $encryptedEmail Encrypted email value
     *
     * @return string Decrypted email value
     */
    public function decryptEmailValue($encryptedEmail);

    /**
     * @param string $email Email to be encrypt/decrypt
     *
     * @return $this
     */
    public function setEmail($email);

    /**
     * @param string $userConfirmationToken
     *
     * @return $this
     */
    public function setUserConfirmationToken($userConfirmationToken);
}
