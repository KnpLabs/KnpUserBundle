Logging by Username or Email
============================

The `UserProviderInterface` implementation provided by FOSUserBundle through
the UserManager uses the username to load the user. Allowing the user to
use both its username and its email to login is simple and can be done achieved
in 2 ways.

## Wrapping the UserManager in another UserProvider

The first way to achieve it is to create a custom USerProvider wrapping the
UserManager. The class will look like this:

```php
<?php

namespace Acme\UserBundle\Security\Provider;

use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class MyProvider implements UserProviderInterface
{
    private $userManager;

    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    public function loadUserByUsername($username)
    {
        $user = $this->userManager->findUserByUsernameOrEmail($username);

        if (!$user) {
            throw new UsernameNotFoundException(sprintf('No user with name "%s" was found.', $username));
        }

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        return $this->userManager->refreshUser($user);
    }

    public function supportsClass($class)
    {
        return $this->userManager->supports($class);
    }
}
```

You now need to register a new service for your provider:

```yaml
# src/Acme/UserBundle/Resources/config/services.yml
services:
    acme_user.my_provider:
        class: Acme\UserBundle\Security\Provider\MyProvider
        public: false
        arguments: ["@fos_user.user_manager"]
```

You can now configure SecurityBundle to use your own service as provider
instead of using the `UserManager`:

```yaml
# app/config/security.yml
security:
    providers:
        custom:
            id: acme_user.my_provider
    # the firewall config is omitted here.
```

## Extending the UserManager class

The other solution is to replace the default `UserManagerInterface` implementation
by your own one extending it. The class would look like this:

```php
<?php

namespace Acme\UserBundle\Model;

// choose the good base class depending of your driver
use FOS\UserBundle\Entity\UserManager;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class MyUserManager extends UserManager
{
    public function loadUserByUsername($username)
    {
        $user = $this->findUserByUsernameOrEmail($username);

        if (!$user) {
            throw new UsernameNotFoundException(sprintf('No user with name "%s" was found.', $username));
        }

        return $user;
    }
}
```

Then register your user manager as a service:

```yaml
# src/Acme/UserBundle/Resources/config/services.yml
services:
    acme_user.my_user_manager:
        class: Acme\UserBundle\Model\MyUserManager
        public: false
        parent: fos_user.user_manager.default
```

**Note:**

> To avoid redefining all constructor argument, we use the definition inheritance
> allowing as to reuse the parent definition and to replace only the needed parts.

Finally let FOSUserBundle know that it should use your own service:

```yaml
# app/config/config.yml
fos_user:
    service:
        user_manager: acme_user.my_user_manager
```

**Comparison with the previous method:**

*Drawbacks of this way to to go:*

- Your own user manager must use the good base class according to your driver.
- You are replacing the UserManager so every part of the code using it will
  receive the modified version. This could potentially create some issues.

*Advantage of this way to go:*

- The code is shorter.
