<?php

namespace Bundle\FOS\UserBundle\Tests\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Bundle\FOS\UserBundle\DependencyInjection\Compiler\InjectParametersIntoEncoderFactoryPass;

class InjectParametersIntoEncoderFactoryPassTest extends \PHPUnit_Framework_TestCase
{
    public function testProcessInjectsEncoderParameters()
    {
        $injector = new InjectParametersIntoEncoderFactoryPass();

        $container = new ContainerBuilder(new ParameterBag(array(
            'security.encoder.digest.class'     => 'digest.class',
            'fos_user.encoder.algorithm'        => 'algorithm',
            'fos_user.encoder.encode_as_base64' => 'encode_as_base64',
            'fos_user.encoder.iterations'       => 'iterations',
        )));

        $definition = new Definition('factory.class', array('factory.encoderMap'));
        $container->setDefinition('security.encoder_factory.generic', $definition);

        $injector->process($container);

        $expectedArguments = array(
            'digest.class',
            array(
                'algorithm',
                'encode_as_base64',
                'iterations',
            ),
            'factory.encoderMap',
        );

        $this->assertEquals($expectedArguments, $definition->getArguments());
    }
}