FOSUserBundle Emails
====================

The FOSUserBundle supports sending emails to a user when various actions are 
taken, such as confirming a registration or requesting a password reset.

The bundle comes with two mailer implementations. They are listed below by 
service id:

- `fos_user.mailer.default` is the default implementation, and uses Swiftmailer to send emails.
- `fos_user.mailer.noop` is a mailer implementation which performs no operation, so no emails are sent.

### Using A Custom Mailer

The default mailer service used by FOSUserBundle relies on the Swiftmailer 
library to send mail. If you would like to use a different library to send 
mails, want to send HTML emails or simply change the content of the email you 
may do so by defining your own service.
 
First you must create a new class which implements `FOS\UserBundle\Mailer\MailerInterface`.

``` php
<?php
// src/Acme/DemoBundle/Mailer/Mailer.php

namespace Acme\DemoBundle\Mailer;

use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Model\UserInterface;

class Mailer implements MailerInterface
{
    public function sendConfirmationEmailMessage(UserInterface $user)
    {
        // send the confirmation email message here
    }

    public function sendResettingEmailMessage(UserInterface $user)
    {
        // send the resetting email message here
    }
}
```

Now you must define your new class as a service in the service container.

In YAML:

``` yaml
# app/config/config.yml
services:
    acme.mailer:
        class: Acme\DemoBundle\Mailer\Mailer
```

In XML:

``` xml
<!-- app/config/config.xml -->
<container xmlns="http://symfony.com/schema/dic/services"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">

    <services>

        <service id="acme.mailer" class="Acme\DemoBundle\Mailer\Mailer" />

    </services>

</container>

```

After you have implemented your custom mailer class and defined it as a service, 
you must update your bundle configuration so that FOSUserBundle will use it. 
Simply set the `mailer` configuration parameter under the `service` section. 
An example is listed below.

In YAML:

``` yaml
fos_user:
    # ...
    service:
        mailer: acme.mailer
```

