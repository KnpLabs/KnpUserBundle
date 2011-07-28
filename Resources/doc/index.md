FOSUserBundle Documentation
===========================

The Symfony2 security component provides a flexible security framework that
allows you to load users from configuration, a database, or anywhere else
you can imagine. The `FOSUserBundle` builds on top of this to make it quick
and easy to store users in a database.

So, if you need to persist and fetch the users in your system to and from
a database, then you're in the right place.

## Installation

Installation is a quick (I promise) 7 step process:

1. Download FOSUserBundle
2. Configure the Autoloader
3. Enable the Bundle
4. Create your User class
5. Configure your application's security.yml
6. Configure the FOSUserBundle
7. Import FOSUserBundle routing

### Step 1: Download FOSUserBundle

Ultimately, the FOSUserBundle files should be downloaded to the 
`vendor/bundles/FOS/UserBundle` directory.

This can be done in several ways, depending on your preference. The first
method is the standard Symfony method for doing this

**Using the vendors script**

Add the following lines in your `deps` file:

    [FOSUserBundle]
        git=git://github.com/FriendsOfSymfony/FOSUserBundle.git
        target=bundles/FOS/UserBundle

Now, run the vendors script to download the bundle:

    php bin/vendors install

**Using submodules**
 
If you prefer instead to use git submodules, the run the following:

``` bash
$ git submodule add git://github.com/FriendsOfSymfony/FOSUserBundle.git vendor/bundles/FOS/UserBundle
$ git submodule update --init
```

### Step 2: Configure the Autoloader

Add the `FOS` namespace to your autoloader:

``` php
<?php
// app/autoload.php

$loader->registerNamespaces(array(
    // ...

    'FOS' => __DIR__.'/../vendor/bundles',
));
```

### Step 3: Enable the bundle

Finally, enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...

        new FOS\UserBundle\FOSUserBundle(),
    );
}
```

### Step 4: Create your User class

The goal of this bundle is to persist some `User` class to a database (MySql,
MongoDB, CouchDB, etc). Your first job, then, is to create the `User` class
for your application. This class can look and act however you want: add any
properties or methods you find useful. This is *your* `User` class.

This class has just two requirements, which allow it to take advantage of
all of the functionality in the FOSUserBundle:

1. It must extend one of the base `User` classes from the bundle
2. It must have an `id` field

In the following sections, you'll see examples of how your `User` class should
look, depending on how you're storing your users (Doctrine ORM, MongoDB ODM,
or CouchDB ODM).

Your `User` class can live inside any bundle in your application. For example,
if you work at "Acme" company, then you might create a bundle called `AcmeUserBundle`
and place your `User` class in it.

**Warning:**

    If you override the `__construct()` method in your `User` class, be sure
    to call `parent::__construct()`, as the base `User` class depends on
    this to initialize some fields.

**a) Doctrine ORM User class**

If you're persisting your users via the Doctrine ORM, then your `User` class
should live in the `Entity` namespace of your bundle and look like this to
start:

``` php
<?php
// src/Acme/UserBundle/Entity/User.php

namespace Acme\UserBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }
}
```

**Note**: `User` is a reserved keyword in SQL so you cannot use it as table name.

**b) MongoDB User class**

If you're persisting your users via the Doctrine MongoDB ODM, then your `User`
class should live in the `Document` namespace of your bundle and look like
this to start:

``` php
<?php
// src/Acme/UserBundle/Document/User.php

namespace Acme\UserBundle\Document;

use FOS\UserBundle\Document\User as BaseUser;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document
 */
class User extends BaseUser
{
    /** 
     * @MongoDB\Id(strategy="auto")
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }
}
```

**c) CouchDB User class**

If you're persisting your users via the Doctrine CouchDB ODM, then your `User`
class should live in the `Document` namespace of your bundle and look like
this to start:

``` php
<?php
// src/Acme/UserBundle/Document/User.php

namespace Acme\UserBundle\Document;

use FOS\UserBundle\Document\User as BaseUser;
use Doctrine\ODM\CouchDB\Mapping as CouchDB;

/**
 * @CouchDB\Document
 */
class User extends BaseUser
{
    /** 
     * @CouchDB\Id 
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }
}
```

### Step 5: Configure your application's security.yml

In order to for the security component of Symfony2 to use FOSUserBundle, you must 
tell it to do so in the `security.yml` file. The `security.yml` file is where the 
basic configuration for the security for your application is contained.

**Note**:

```
The UserBundle works with the Symfony Security Component, so make sure that the 
`SecurityBundle` is registered in your `AppKernel` and enabled in your application's 
configuration. A working security configuration using `FOSUserBundle` is available 
at the end of this document.
```

Below is the minimum configuration necessary in your application's `security.yml` 
file to get the bundle up and running:

``` yml
# app/config/security.yml
security:
    providers:
        fos_userbundle:
            id: fos_user.user_manager

    firewalls:
        main:
            pattern: .*
            form_login:
                provider: fos_userbundle
            logout:       true
            anonymous:    true
```

Let's quickly go over the configuration above. First, under the `providers` section, 
you have created a new user provider with the id of `fos_userbundle`. This 
is simply the name used to identify this provider throughout the rest of the configuration 
file. By specifying `id: fos_user.user_manager`, you have told the Symfony2 framework 
to use the service configured in the Dependency Injection Container (DIC) with the id of 
`fos_user.user_manager` as the actual object instance to perform the duties of the user 
provider. These duties include loading a user from the datastore, etc.

Now that we have declared our new user provider, lets examine the `firewalls` section. 
Here we have declared a firewall named `main`. By specifying `form_login`, you have told 
the Symfony2 framework that any time a request is made to this firewall that leads to 
the user needeing to authenticate himself, the user will be redirected to a form where he 
will be able to enter his credentials. It should come as no surprise then that you have 
specified the user provider we declared earlier as the provider for the firewall to use 
as part of the authentication process.

In order to actually secure a routing pattern you must configure the `access_control` 
section of the security configuration. Please see the section titled 
`Verbose Security Configuration` at the bottom of this document for examples.

For more information on configuring the `security.yml` file please read the Symfony2 
security component [documentation](http://symfony.com/doc/current/book/security.html).

**Note:**

```
Pay close attention to the name, `main`, that we have given to the firewall which 
the FOSUserBundle is configured in. You will use this in the next step when you 
configure the FOSUserBundle.
```

### Step 6: Configure the FOSUserBundle

Now that you have properly configured your application `security.yml` file for use 
with the `FOSUserBundle`, you can move on to configuring the bundle to work with 
the specific needs of your application.

The `FOSUserBundle` has many configuration options, but for now we will take a look 
at the minimal configuration required to get you up and running.

Your configuration must define your DB driver ("orm", "mongodb", or "couchdb"), 
the fully qualified class name (FQCN) of the `User` class which you created in Step 2, 
and the firewall name which you configured in Step 5.

Now lets take a look at the minimal configuration necessary for the bundle:

**a) FOSUserBundle config for ORM**

``` yml
# app/config/config.yml
fos_user:
    db_driver: orm
    firewall_name: main
    user_class: MyProject\MyBundle\Entity\User
```

Or if you prefer XML:

``` xml
# app/config/config.xml
<fos_user:config
    db-driver="orm"
    firewall-name="main"
    user-class="MyProject\MyBundle\Entity\User"
/>
```

**b) FOSUserBundle config for MongoDB**

In YAML:

``` yml
# app/config/config.yml
fos_user:
    db_driver: mongodb
    firewall_name: main
    user_class: MyProject\MyBundle\Document\User
```

Or if you prefer XML:

``` xml
# app/config/config.xml
<fos_user:config
    db-driver="mongodb"
    firewall-name="main">
    user-class="MyProject\MyBundle\Document\User"
/>
```

**CouchDB**

In YAML:

``` yml
# app/config/config.yml
fos_user:
    db_driver: couchdb
    firewall_name: main
    user_class: MyProject\MyBundle\Document\User
```

Or if you prefer XML:

``` xml
# app/config/config.xml
<fos_user:config
    db-driver="couchdb"
    firewall-name="main"
    user-class="MyProject\MyBundle\Document\User"
/>
```

That's it. The minimal configuration of the bundle itself is very straightforward. 
For a verbose bundle configuration reference, please see the section titled 
`Verbose FOSUserBundle Configuration` located at the bottom of this document.

### Step 7: Import FOSUserBundle routing files

Now that you have activated and configured the bundle, all that is left to do is 
import the `FOSUserBundle` routing files.

**a) Import FOSUserBundle routing files**

If you want ready to use login and logout pages, include the built-in
routes:

In YAML:

``` yml
# app/config/routing.yml
fos_user_security:
    resource: "@FOSUserBundle/Resources/config/routing/security.xml"

fos_user_profile:
    resource: "@FOSUserBundle/Resources/config/routing/profile.xml"
    prefix: /profile

fos_user_register:
    resource: "@FOSUserBundle/Resources/config/routing/registration.xml"
    prefix: /register

fos_user_resetting:
    resource: "@FOSUserBundle/Resources/config/routing/resetting.xml"
    prefix: /resetting

fos_user_change_password:
    resource: "@FOSUserBundle/Resources/config/routing/change_password.xml"
    prefix: /change-password
```

Or if you prefer XML:

``` xml
# app/config/routing.xml
<import resource="@FOSUserBundle/Resources/config/routing/security.xml"/>
<import resource="@FOSUserBundle/Resources/config/routing/profile.xml" prefix="/profile" />
<import resource="@FOSUserBundle/Resources/config/routing/registration.xml" prefix="/register" />
<import resource="@FOSUserBundle/Resources/config/routing/resetting.xml" prefix="/resetting" />
<import resource="@FOSUserBundle/Resources/config/routing/change_password.xml" prefix="/change-password" />
```

The login form and all the routes used to create a user and reset the password
have to be available to unauthenticated users but using the same firewall as
the pages you want to securize with the bundle. Assuming you import the
`registration.xml` routing file with the `/register` prefix and `resetting.xml`
with the `/resetting` prefix they will be:

```
/login
/register/
/register/check-email
/register/confirm/{token}
/register/confirmed
/resetting/request
/resetting/send-email
/resetting/check-email
/resetting/reset/{token}
```

You now can login at `http://app.com/app_dev.php/login`.

**Note:**

```
You need to activate SwiftmailerBundle to be able to use the functionalities
using emails (confirmation of the account, resetting of the password).
```

Command line
============

FOSUserBundle provides command line utilities to help manage your
application users.

Create user
-----------

This command creates a new user::

    $ php app/console fos:user:create username email password

If you don't provide the required arguments, a interactive prompt will
ask them to you::

    $ php app/console fos:user:create

Promote user as a super administrator
-------------------------------------

This command promotes a user as a super administrator::

    $ php app/console fos:user:promote

User manager service
====================

FOSUserBundle works with both ORM and ODM. To make it possible, it wraps
all the operation on users in a UserManager. The user manager is a service
of the container.

If you configure the db_driver to orm, this service is an instance of
``FOS\UserBundle\Entity\UserManager``.

If you configure the db_driver to mongodb, this service is an instance of
``FOS\UserBundle\Document\UserManager``.

If you configure the db_driver to couchdb, this service is an instance of
``FOS\UserBundle\CouchDocument\UserManager``.

All these classes implement ``FOS\UserBundle\Model\UserManagerInterface``.

Access the user manager service
-------------------------------

If you want to manipulate users in a way that will work as well with
ORM and ODM, use the fos_user.user_manager service::

    $userManager = $container->get('fos_user.user_manager');

That's the way FOSUserBundle's internal controllers are built.

Create a new user
-----------------

A new instance of your User class can be created by the user manager::

    $user = $userManager->createUser();

`$user` is now an Entity or a Document, depending on the configuration.

Updating a User object
----------------------

When creating or updating a User object you need to update the encoded password
and the canonical fields. To make it easier, the bundle comes with a Doctrine
listener handling this for you behind the scene.

If you don't want to use the Doctrine listener, you can disable it. In this case
you will have to call the ``updateUser`` method of the user manager each time
you do a change in your entity.

In YAML:

::

    # app/config/config.yml
    fos_user:
        db_driver: orm
        firewall_name: main
        use_listener: false
        user_class: MyProject\MyBundle\Entity\User

Or if you prefer XML:

::

    # app/config/config.xml

    <fos_user:config
        db-driver="orm"
        firewall-name="main"
        use-listener="false"
        user-class="MyProject\MyBundle\Entity\User"
    />

.. note::

    The default behavior is to flush the changes when calling this method. You
    can disable the flush when using the ORM and the MongoDB implementations by
    passing a second argument set to ``false``.

Using groups
============

The bundle allows to optionally use groups. You need to explicitly
enable it in your configuration by giving the Group class which must
implement ``FOS\UserBundle\Model\GroupInterface``.

In YAML:

::

    # app/config/config.yml
    fos_user:
        db_driver: orm
        firewall_name: main
        user_class: MyProject\MyBundle\Entity\User
        group:
            group_class: MyProject\MyBundle\Entity\Group

Or if you prefer XML:

::

    # app/config/config.xml

    <fos_user:config
        db-driver="orm"
        firewall-name="main"
        user-class="MyProject\MyBundle\Entity\User"
    >
        <fos_user:group group-class model="MyProject\MyBundle\Entity\Group" />
    </fos_user:config>

The Group class
---------------

The simpliest way is to extend the mapped superclass provided by the
bundle.

ORM
~~~

::

    // src/MyProject/MyBundle/Entity/Group.php

    <?php
    namespace MyProject\MyBundle\Entity;
    use FOS\UserBundle\Entity\Group as BaseGroup;
    use Doctrine\ORM\Mapping as ORM;

    /**
     * @ORM\Entity
     * @ORM\Table(name="fos_group")
     */
    class Group extends BaseGroup
    {
        /**
         * @ORM\Id
         * @ORM\Column(type="integer")
         * @ORM\generatedValue(strategy="AUTO")
         */
        protected $id;
    }

.. note::

    ``Group`` is also a reserved keyword in SQL so it cannot be used either.

MongoDB
~~~~~~~

::

    // src/MyProject/MyBundle/Document/Group.php

    <?php
    namespace MyProject\MyBundle\Document;
    use FOS\UserBundle\Document\Group as BaseGroup;
    use Doctrine\ODM\MongoDB\Mapping as MongoDB;

    /**
     * @MongoDB\Document
     */
    class Group extends BaseGroup
    {
        /** @MongoDB\Id(strategy="auto") */
        protected $id;
    }

CouchDB
~~~~~~~

::

    // src/MyProject/MyBundle/Document/Group.php

    namespace MyProject\MyBundle\Document;
    use FOS\UserBundle\Document\Group as BaseGroup;
    use Doctrine\ODM\CouchDB\Mapping as MongoDB;

    /**
     * @CouchDB\Document
     */
    class Group extends BaseGroup
    {
        /** @CouchDB\Id */
        protected $id;
    }

Defining the relation
---------------------

The next step is to map the relation in your User class.

ORM
~~~

::

    // src/MyProject/MyBundle/Entity/User.php

    <?php
    namespace MyProject\MyBundle\Entity;
    use FOS\UserBundle\Entity\User as BaseUser;
    use Doctrine\ORM\Mapping as ORM;

    /**
     * @ORM\Entity
     * @ORM\Table(name="fos_user")
     */
    class User extends BaseUser
    {
        /**
         * @ORM\Id
         * @ORM\Column(type="integer")
         * @ORM\generatedValue(strategy="AUTO")
         */
        protected $id;

        /**
         * @ORM\ManyToMany(targetEntity="MyProject\MyBundle\Entity\Group")
         * @ORM\JoinTable(name="fos_user_user_group",
         *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
         *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
         * )
         */
        protected $groups;
    }

MongoDB
~~~~~~~

::

    // src/MyProject/MyBundle/Document/User.php

    <?php
    namespace MyProject\MyBundle\Document;
    use FOS\UserBundle\Document\User as BaseUser;
    use Doctrine\ODM\MongoDB\Mapping as MongoDB;

    /**
     * @MongoDB\Document
     */
    class User extends BaseUser
    {
        /** @MongoDB\Id(strategy="auto") */
        protected $id;

        /** @MongoDB\ReferenceMany(targetDocument="MyProject\MyBundle\Document\Group") */
        protected $groups;
    }

CouchDB
~~~~~~~

::

    // src/MyProject/MyBundle/Document/User.php

    namespace MyProject\MyBundle\Document;
    use FOS\UserBundle\Document\User as BaseUser;
    use Doctrine\ODM\CouchDB\Mapping as CouchDB;

    /**
     * @CouchDB\Document
     */
    class User extends BaseUser
    {
        /** @CouchDB\Id */
        protected $id;

        /** @CouchDB\ReferenceMany(targetDocument="MyProject\MyBundle\Document\Group") */
        protected $groups;
    }

Enabling the routing for the GroupController
--------------------------------------------

You can also the group.xml file to use the builtin controller to manipulate the
groups.

Configuration example
=====================

This section provides a working configuration for the bundle and the security.

FOSUserBundle configuration
---------------------------

::

    # app/config/config.yml
    fos_user:
        db_driver:     orm
        firewall_name: main
        user_class:  MyProject\MyBundle\Entity\User

Security configuration
----------------------

::

    # app/config/security.yml
    security:
        providers:
            fos_userbundle:
                id: fos_user.user_manager

        firewalls:
            # Disabling the security for the web debug toolbar, the profiler and Assetic.
            dev:
                pattern:  ^/(_(profiler|wdt)|css|images|js)/
                security: false

            main:
                pattern:      .*
                form_login:
                    provider:       fos_userbundle
                    login_path:     /login
                    use_forward:    false
                    check_path:     /login_check
                    failure_path:   null
                logout:       true
                anonymous:    true

        access_control:
            # URL of FOSUserBundle which need to be available to anonymous users
            - { path: ^/login$, role: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/register, role: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/resetting, role: IS_AUTHENTICATED_ANONYMOUSLY }
            # Secured part of the site
            # This config requires being logged for the whole site and having the admin role for the admin part.
            # Change these rules to adapt them to your needs
            - { path: ^/admin/, role: ROLE_ADMIN }
            - { path: ^/.*, role: ROLE_USER }

        role_hierarchy:
            ROLE_ADMIN:       ROLE_USER
            ROLE_SUPER_ADMIN:  ROLE_ADMIN

Replacing some part by your own implementation
==============================================

User Manager
------------

You can replace the default implementation of the user manager by defining
a service implementing ``FOS\UserBundle\Model\UserManagerInterface`` and
setting its id in the configuration::

    fos_user:
        # ...
        service:
            user_manager: custom_user_manager_id

Templating
----------

The template names are not configurable, however Symfony2 makes it possible
to extend a bundle by defining a template in the app/ directory.

For example ``vendor/bundles/FOS/UserBundle/Resources/views/Registration/register.html.twig``
can be replaced inside an application by putting a file with alternative content
in ``app/Resources/FOSUserBundle/views/Registration/register.html.twig``.

You could also create a bundle defined as child of FOSUserBundle and placing the
templates in it as ``src/Acme/ChildBundle/Resources/views/Registration/register.html.twig``.

You can use a different templating engine by configuring it but you will have to
create all the needed templates as only twig templates are provided.

Controller
----------

Create a bundle defined as child of FOSUserBundle::

    // src/Acme/ChildBundle/AcmeChildBundle.php
    <?php

    namespace Acme\ChildBundle;

    use Symfony\Component\HttpKernel\Bundle\Bundle;

    class AcmeChildBundle extends Bundle
    {
        public function getParent()
        {
            return 'FOSUserBundle';
        }
    }

Then overwritting a controller is just a matter of creating a controller
with the same name in this bundle (e.g. ``Acme\ChildBundle\Controller\ProfileController``
to overwrite the ProfileController provided by FOSUserBundle).
You can of course make your controller extend the controller of the bundle
if you want to change only some methods.

Validation
----------

The ``Resources/config/validation.xml`` file contains definitions for custom
validator rules for various classes. The rules defined by FOSUserBundle are
all in validation groups so you can choose not to use them.

Emails
------

The default mailer relies on Swiftmailer to send the mails of the bundle.
If you want to use another mailer in your project you can change it by defining
your own service implementing ``FOS\UserBundle\Mailer\MailerInterface`` and
setting its id in the configuration::

    fos_user:
        # ...
        service:
            mailer: custom_mailer_id

This bundle comes with two mailer implementations.

- `fos_user.mailer.default` is the default implementation, and uses swiftmailer to send emails.
- `fos_user.mailer.noop` does nothing and can be used if your project does not depend on swiftmailer.

Canonicalization
----------------

``Canonicalizer`` services are used to canonicalize the username and the email
fields for database storage. By default, username and email fields are
canonicalized in the same manner using ``mb_convert_case()``. You may configure
your own class for each field provided it implements
``FOS\UserBundle\Util\CanonicalizerInterface``.

.. note::

    If you do not have the mbstring extension installed you will need to
    define your own ``canonicalizer``.

Use the username form type
==========================

The bundle also provides a convenient username form type.
It appears as a text input, accepts usernames and convert them to a User instance.

You can enable this feature from the configuration::

    # app/config/config.yml
    fos_user:
        use_username_form_type: true

And then use it in your forms::

    class MessageFormType extends AbstractType
    {
        public function buildForm(FormBuilder $builder, array $options)
        {
            $builder->add('recipient', 'fos_user_username');
        }


Configuration reference
=======================

All available configuration options are listed below with their default values::

    # app/config/config.yml
    fos_user:
        db_driver:      ~ # Required
        firewall_name:  ~ # Required
        user_class:     ~ # Required
        use_listener:   true
        use_username_form_type: false
        from_email:
            address:        webmaster@example.com
            sender_name:    Admin
        profile:
            form:
                type:               fos_user_profile
                handler:            fos_user.profile.form.handler.default
                name:               fos_user_profile_form
                validation_groups:  [Profile]
        change_password:
            form:
                type:               fos_user_change_password
                handler:            fos_user.change_password.form.handler.default
                name:               fos_user_change_password_form
                validation_groups:  [ChangePassword]
        registration:
            confirmation:
                from_email: # Use this node only if you don't want the global email address for the confirmation email
                    address:        ...
                    sender_name:    ...
                enabled:    false
                template:   FOSUserBundle:Registration:email.txt.twig
            form:
                type:               fos_user_registration
                handler:            fos_user.registration.form.handler.default
                name:               fos_user_registration_form
                validation_groups:  [Registration]
        resetting:
            token_ttl: 86400
            email:
                from_email: # Use this node only if you don't want the global email address for the resetting email
                    address:        ...
                    sender_name:    ...
                template:   FOSUserBundle:Resetting:email.txt.twig
            form:
                type:               fos_user_resetting
                handler:            fos_user.resetting.form.handler.default
                name:               fos_user_resetting_form
                validation_groups:  [ResetPassword]
        service:
            mailer:                 fos_user.util.mailer.default
            email_canonicalizer:    fos_user.util.email_canonicalizer.default
            username_canonicalizer: fos_user.util.username_canonicalizer.default
            user_manager:           fos_user.user_manager.default
        encoder:
            algorithm:          sha512
            encode_as_base64:   false
            iterations:         1
        template:
            engine: twig
            theme:  FOSUserBundle::form.html.twig
        group:
            group_class:    ~ # Required when using groups
            form:
                type:               fos_user_group
                handler:            fos_user.group.form.handler.default
                name:               fos_user_group_form
                validation_groups:  [Registration]
