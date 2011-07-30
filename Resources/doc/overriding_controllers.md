Overriding Default FOSUserBundle Controllers
============================================

The default controllers packaged with the FOSUserBundle provide a lot of 
functionality that is sufficient for general use cases. But, you might find 
that you need to extend that functionality and add some logic that suits the 
specific needs of your application.

The first step to overriding a controller in the bundle is to create a child 
bundle whose parent is FOSUserBundle. The following code snippet creates a new 
bundle named `AcmeUserBundle` that declares itself of a child of FOSUserBundle.

``` php
// src/Acme/UserBundle/AcmeUserBundle.php
<?php

namespace Acme\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AcmeUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
```

Now that you have created the new child bundle you can simply create a controller class 
with the same name and in the same location as the one you want to override. This 
example overrides the `RegistrationController` by extending the FOSUserBundle 
`RegistrationController` class and simply overriding the method that needs the extra 
functionality.

``` php
// src/Acme/UserBundle/Controller/RegistrationController.php
<?php

namespace Acme\UserBundle\Controller;

use FOS\UserBundle\Controller\RegistrationController as Controller;

class RegistrationController extends Controller
{
    public function registerAction()
    {
        // now this method will be called for the register action 
        // instead of the method in the FOSUserBundle
    }
}
```

**Note:**

```
If you do not extend the FOSUserBundle controller class that you want to override 
and instead extend ContainerAware or the Controller class provided by the FrameworkBundle 
then you must implement all of the methods of the FOSUserBundle controller that 
you are overriding.
```
