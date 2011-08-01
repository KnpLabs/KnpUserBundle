Overriding Default FOSUserBundle Forms
======================================

The default forms packaged with the FOSUserBundle provide functionality for 
registering a new user, updating a user's information, changing a password and 
much more. These forms work well with the default classes and controllers provided 
by the bundle. But, as you start to add more properties to your `User` 
class or you decide you want to add a few options to the registration form you 
will find that you need to override the default forms provided by the bundle.

Suppose that you have created an ORM user class with the following class name, 
`Acme\UserBundle\Entity\User`. In this class, you have added a `name` property 
because you would like to save the user's name as well as their username and 
email address. Now, when a user registers for your site they should enter in their 
name as well as their username, email and password.

The first thing you will need to do is create a new form type in your bundle. 
The following class extends the base FOSUserBundle `RegistrationFormType` and 
then adds the custom `name` field.

``` php
// src/Acme/UserBundle/Form/Type/RegistrationFormType.php
<?php

namespace Acme\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilder;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class RegistrationFormType extends BaseType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        parent::buildForm($builder, $options);

        // add your custom field
        $builder->add('name');
    }

    public function getName()
    {
        return 'acme_user_registration';
    }
}
```

Now that you have created your custom form type, you must declare it as a service 
and tag it with `form.type` and give it an alias. The alias that you specify is 
what you will use in the FOSUserBundle configuration to let the bundle know 
that you want to use your custom form instead of the default.

Below is an example of configuring your form type as a service in XML:

``` xml
<!-- src/Acme/UserBundle/Resources/config/services.xml -->
<?xml version="1.0" encoding="UTF-8" ?>

<container xmlns="http://symfony.com/schema/dic/services"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">

    <services>

        <service id="acme_user.registration.form.type" class="Acme\UserBundle\Form\Type\RegistrationFormType">
            <tag name="form.type" alias="acme_user_registration" />
            <argument>%fos_user.model.user.class%</argument>
        </service>

    </services>

</container>
```

**Note:**

```
In the form type service configuration you have specified the `fos_user.model.user.class` 
container parameter as a constructor argument. This is a requirement of the 
FOSUserBundle form type that you extended. If instead, you just extend the 
AbstractType provided by the Symfony2 Form component, added all of the fields 
to your form yourself and defined the data_class option for your form type, 
then this argument will not be necessary.
```

Finally, you must configure the FOSUserBundle so that it knows to use your form 
type instead of the default one. Below is the configuration for changing the 
registration form type in YAML.

``` yaml
# app/config/config.yml
fos_user:
    # ...
    registration:
        form:
            type: acme_user_registration
```

Note how the `alias` value used in the service configuration is used here to tell 
the FOSUserBundle to use your custom form type.
