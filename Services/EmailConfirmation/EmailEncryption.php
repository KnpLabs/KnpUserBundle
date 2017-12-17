<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Services\EmailConfirmation;

use FOS\UserBundle\Services\EmailConfirmation\Interfaces\EmailEncryptionInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class EmailEncryption.
 *
 * Use this class for encryption/decryption of email value based on specified
 * token.
 */
class EmailEncryption implements EmailEncryptionInterface
{
    /**
     * @var string
     */
    private $encryptionMode;

    /**
     * @var string User confirmation token. Use for email encryption
     */
    private $userConfirmationToken;

    /**
     * @var string Email value to be encrypted
     */
    private $email;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * EmailEncryption cypher method (see http://php.net/manual/function.openssl-get-cipher-methods.php ).
     *
     * @param ValidatorInterface $validator
     * @param string             $mode
     */
    public function __construct(ValidatorInterface $validator, $mode = null)
    {
        $this->validator = $validator;
        if (!$mode) {
            $mode = openssl_get_cipher_methods(false)[0];
        }
        $this->encryptionMode = $mode;
    }

    /**
     * Encrypt email value with specified user confirmation token.
     *
     * @return string Encrypted email
     */
    public function encryptEmailValue()
    {
        $iv = openssl_random_pseudo_bytes($this->getIvSize());

        $encryptedEmail = openssl_encrypt(
            $this->email,
            $this->encryptionMode,
            $this->getConfirmationToken(),
            0,
            $iv
        );

        $encryptedEmail = base64_encode($iv.$encryptedEmail);

        return $encryptedEmail;
    }

    /**
     * Decrypt email value with specified user confirmation token.
     *
     * @param string $encryptedEmail
     *
     * @return string Decrypted email
     */
    public function decryptEmailValue($encryptedEmail)
    {
        $b64DecodedEmailHash = base64_decode($encryptedEmail);
        $ivSize = $this->getIvSize();

        // Select IV part from encrypted value
        $iv = substr($b64DecodedEmailHash, 0, $ivSize);

        // Select email part from encrypted value
        $preparedEncryptedEmail = substr($b64DecodedEmailHash, $ivSize);

        $decryptedEmail = openssl_decrypt(
            $preparedEncryptedEmail,
            $this->encryptionMode,
            $this->getConfirmationToken(),
            0,
            $iv
        );

        // Trim decrypted email from nul byte before return
        $email = rtrim($decryptedEmail, "\0");

        /** @var ConstraintViolationList $violationList */
        $violationList = $this->validator->validate($email, new Email());
        if ($violationList->count() > 0) {
            throw new \InvalidArgumentException('Wrong email format was provided for decryptEmailValue function');
        }

        return $email;
    }

    /**
     * Set user confirmation token. Will be used for email encryption/decryption.
     * User confirmation token size should be either 16, 24 or 32 byte.
     *
     * @param string $userConfirmationToken
     *
     * @return $this
     */
    public function setUserConfirmationToken($userConfirmationToken)
    {
        if (!$userConfirmationToken || !is_string($userConfirmationToken)) {
            throw new \InvalidArgumentException(
                'Invalid user confirmation token value.'
            );
        }

        $this->userConfirmationToken = $userConfirmationToken;

        return $this;
    }

    /**
     * Set email value to be encrypted.
     *
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email)
    {
        if (!is_string($email)) {
            throw new \InvalidArgumentException(
                'Email to be encrypted should a string. '
                .gettype($email).' given.'
            );
        }

        $this->email = trim($email);

        return $this;
    }

    /**
     * Get confirmation token.
     *
     * @return string
     */
    public function getConfirmationToken()
    {
        if (!$this->userConfirmationToken) {
            throw new \InvalidArgumentException(
                'User confirmation token should be specified.'
            );
        }

        // Generate the random binary string based on hashed hexadecimal token
        return pack('H*', hash('sha256', $this->userConfirmationToken));
    }

    /**
     * Return IV size.
     *
     * @return int
     */
    protected function getIvSize()
    {
        return openssl_cipher_iv_length($this->encryptionMode);
    }
}
