Upgrade instruction
===================

This document describes the changes needed when upgrading because of a BC
break. For the full list of changes, please look at the Changelog file.

## 1.2 to 2.0

### Forms

The profile form no longer wraps the user in a CheckPassword class. If you
were overriding the form handler, you will need to update it to pass the
user object directly.

### Token generation

The generation of the token is not done by the User class anymore. If you
were using the `generateToken` or `generateConfirmationToken` in your own
code, you need to use the `fos_user.util.token_generator` service to generate
the token.

## 1.1 to 1.2

This file describes the needed changes when upgrading from 1.1 to 1.2

### Removed the user-level algorithm.

If you are experiencing the exception
`No encoder has been configured for account "Acme\DemoBundle\Entity\User"`
after upgrading, please consider the following.

The encoder now needs to be configured in the SecurityBundle configuration
as described in the official documentation. If you were using the default
value of the bundle, the config should look like this to reuse the same settings:

```yaml
#app/config/security.yml
security:
    encoders:
        "FOS\UserBundle\Model\UserInterface":
            algorithm: sha512
            encode_as_base64: false
            iterations: 1
```
