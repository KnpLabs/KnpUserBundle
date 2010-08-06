Provides a Doctrine User Entity and bounds it to the Symfony2 Session service.
Also provides login & logout controllers.

## INSTALLATION

1. Add this bundle to your project as a Git submodule:

    $ git submodule add git://github.com/knplabes/DoctrineUserBundle.git src/Bundle/DoctrineUserBundle

2. Add this bundle to your application's kernel:

    // application/ApplicationKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new Bundle\DoctrineUserBundle\DoctrineUserBundle(),
            // ...
        );
    }

3. Configure the `auth` service in your config:

    # application/config/config.yml
    auth.config: ~
