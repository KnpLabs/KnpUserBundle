Overriding Default FOSUserBundle Validation
===========================================

The `Resources/config/validation.xml` file contains definitions for custom
validator rules for various classes. The rules defined by FOSUserBundle are
all in validation groups so you can choose not to use them.

Form Types
==========

## The username Form Type

The bundle also provides a convenient username form type.
It appears as a text input, accepts usernames and convert them to a User instance.

You can enable this feature from the configuration.

In YAML:

``` yaml
# app/config/config.yml
fos_user:
    use_username_form_type: true
```

And then use it in your forms.

``` php
class MessageFormType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('recipient', 'fos_user_username');
    }
```

FOSUserBundle Canonicalization
==============================

`Canonicalizer` services are used to canonicalize the username and the email
fields for database storage. By default, username and email fields are
canonicalized in the same manner using `mb_convert_case()`. You may configure
your own class for each field provided it implements
`FOS\UserBundle\Util\CanonicalizerInterface`.

**Note:**

```
If you do not have the mbstring extension installed you will need to define your
own `canonicalizer`.
```
