FOSUserBundle
=============

The FOSUserBundle adds support for a database-backed user system in Symfony2+.
It provides a flexible framework for user management that aims to handle
common tasks such as user registration and password retrieval.

Features include:

- Users can be stored via Doctrine ORM or MongoDB/CouchDB ODM
- Registration support, with an optional confirmation per email
- Password reset support
- Unit tested

**Note:** This bundle does *not* provide an authentication system but can
provide the user provider for the core [SecurityBundle](https://symfony.com/doc/current/book/security.html).

[![Build Status](https://github.com/FriendsOfSymfony/FOSUserBundle/workflows/CI/badge.svg?branch=master)](https://github.com/FriendsOfSymfony/FOSUserBundle/actions?query=workflow%3ACI+branch%3Amaster) [![Total Downloads](https://poser.pugx.org/friendsofsymfony/user-bundle/downloads.svg)](https://packagist.org/packages/friendsofsymfony/user-bundle) [![Latest Stable Version](https://poser.pugx.org/friendsofsymfony/user-bundle/v/stable.svg)](https://packagist.org/packages/friendsofsymfony/user-bundle)

Maintenance status
------------------

The package only receives minimal maintenance to allow existing projects to upgrade. Existing projects are expected to plan a migration away from this bundle.

**New projects should not use this bundle.** It is actually much easier to implement the User entity in the project (allowing to fit exactly the need of the project). Regarding the extra features of the bundle:

- the EntityUserProvider of Symfony already provides the UserProvider when using the Doctrine ORM (and other object managers integrated with Symfony have an equivalent feature)
- change password is easy to implement in the project. That's a simple form relying on core Symfony features (the validator for the current password is in core since years)
- email verification is provided by https://github.com/SymfonyCasts/verify-email-bundle
- password reset is provided by https://github.com/SymfonyCasts/reset-password-bundle
- registration is easier to implement in the project directly to fit the need of the project, especially when the User entity is in the project. symfony/form already provides everything you need
- the ProfileController showing a profile page for the user is better done in projects needing it, as the content is likely not enough anyway

Documentation
-------------

The source of the documentation is stored in the `Resources/doc/` folder
in this bundle, and available on symfony.com:

[Read the Documentation for master](https://symfony.com/doc/master/bundles/FOSUserBundle/index.html)

[Read the Documentation for 1.3.x](https://symfony.com/doc/1.3.x/bundles/FOSUserBundle/index.html)

Installation
------------

All the installation instructions are located in the documentation.

License
-------

This bundle is under the MIT license. See the complete license [in the bundle](LICENSE)

About
-----

UserBundle is a [knplabs](https://github.com/knplabs) initiative.
See also the list of [contributors](https://github.com/FriendsOfSymfony/FOSUserBundle/contributors).

Reporting an issue or a feature request
---------------------------------------

Issues and feature requests are tracked in the [Github issue tracker](https://github.com/FriendsOfSymfony/FOSUserBundle/issues).

When reporting a bug, it may be a good idea to reproduce it in a basic project
built using the [Symfony Standard Edition](https://github.com/symfony/symfony-standard)
to allow developers of the bundle to reproduce the issue by simply cloning it
and following some steps.
