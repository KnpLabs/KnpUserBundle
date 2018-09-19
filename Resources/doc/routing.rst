Advanced routing configuration
==============================

By default, the routing file ``@FOSUserBundle/Resources/config/routing/all.xml`` imports
all the routing files (except groups) and enables all the routes.
Use ``@FOSUserBundle/Resources/config/routing_41/all.xml`` for `Symfony4.1`.
In the case you want to enable or disable the different available routes, just use the
single routing configuration files.

Use `routing_41` instead of `routing` for `Symfony4.1`.

.. configuration-block::

    .. code-block:: yaml

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
            prefix: /profile

    .. code-block:: xml

        <!-- app/config/routing.xml -->
        <import resource="@FOSUserBundle/Resources/config/routing/security.xml"/>
        <import resource="@FOSUserBundle/Resources/config/routing/profile.xml" prefix="/profile" />
        <import resource="@FOSUserBundle/Resources/config/routing/registration.xml" prefix="/register" />
        <import resource="@FOSUserBundle/Resources/config/routing/resetting.xml" prefix="/resetting" />
        <import resource="@FOSUserBundle/Resources/config/routing/change_password.xml" prefix="/profile" />
