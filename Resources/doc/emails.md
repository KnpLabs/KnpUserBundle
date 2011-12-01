FOSUserBundle Emails
====================

The FOSUserBundle has built-in support for sending emails in two different 
instances.

### Registration Confirmation

The first is when a new user registers and the bundle is configured 
to require email confirmation before the user registration is complete. 
The email that is sent to the new user contains a link that, when visited, 
will verify the registration and enable the user account.

Requiring email confirmation for a new account is turned off by default. 
To enable it, update your configuration as follows:

``` yaml
# app/config/config.yml

fos_user:
    # ...
    registration:
        confirmation:
            enabled:    true
```

### Password Reset

An email is also sent when a user has requested a password reset. The 
FOSUserBundle provides password reset functionality in a two-step process. 
When a user wishes to reset their password they have to request a password 
reset. When a users does, he is sent an email containing a link to visit to 
reset their password. Upon visiting the link, the user will be identified 
with the token contained in the url. When the appropriate link is visited, 
the user will be presented with a form to enter in a new password.

### Default Mailer Implementations

The bundle comes with two mailer implementations. They are listed below by 
service id:

- `fos_user.mailer.default` is the default implementation, and uses Swiftmailer to send emails.
- `fos_user.mailer.noop` is a mailer implementation which performs no operation, so no emails are sent.

### Using A Custom Mailer

The default mailer service used by FOSUserBundle relies on the Swiftmailer 
library to send mail. If you would like to use a different library to send 
mails, want to send HTML emails or simply change the content of the email you 
may do so by defining your own service.
 
First you must create a new class which implements `FOS\UserBundle\Mailer\MailerInterface` 
which is listed below.

``` php
<?php

namespace FOS\UserBundle\Mailer;

use FOS\UserBundle\Model\UserInterface;

/**
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
interface MailerInterface
{
    /**
     * Send an email to a user to confirm the account creation
     *
     * @param UserInterface $user
     */
    function sendConfirmationEmailMessage(UserInterface $user);

    /**
     * Send an email to a user to confirm the password reset
     *
     * @param UserInterface $user
     */
    function sendResettingEmailMessage(UserInterface $user);
}
```

After you have implemented your custom mailer class and defined it as a service, 
you must update your bundle configuration so that FOSUserBundle will use it. 
Simply set the `mailer` configuration parameter under the `service` section. 
An example is listed below.

In YAML:

``` yaml
# app/config/config.yml

fos_user:
    # ...
    service:
        mailer: acme.mailer
```

To see an example of a working implementation of the `MailerInterface` see 
the [ZetaMailer](https://github.com/simplethings/ZetaWebmailBundle/blob/master/UserBundle/ZetaMailer.php) 
class of the [ZetaWebmailBundle](https://github.com/simplethings/ZetaWebmailBundle). 
This implementation uses the Zeta Components Mail to send emails instead of 
Swiftmailer.

