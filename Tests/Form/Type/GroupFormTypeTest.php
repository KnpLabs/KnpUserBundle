<?php

namespace FOS\UserBundle\Tests\Form\Type;

use FOS\UserBundle\Form\Type\GroupFormType;
use FOS\UserBundle\Tests\TestGroup;

class GroupFormTypeTest extends TypeTestCase
{
    public function testSubmit()
    {
        $group = new TestGroup('foo');

        $form = $this->factory->create(new GroupFormType('FOS\UserBundle\Tests\TestGroup'), $group);
        $formData = array(
            'name'      => 'bar',
        );
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($group, $form->getData());
        $this->assertEquals('bar', $group->getName());
    }
}
