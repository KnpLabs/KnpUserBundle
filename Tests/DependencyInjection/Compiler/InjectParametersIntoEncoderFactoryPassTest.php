<?php

namespace Bundle\FOS\UserBundle\Tests\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Bundle\FOS\UserBundle\DependencyInjection\Compiler\InjectParametersIntoEncoderFactoryPass;

class InjectParametersIntoEncoderFactoryPassTest extends \PHPUnit_Framework_TestCase
{
    public function testProcessInjectsEncoderParameters()
    {
        $container = new ContainerBuilder();
        $definition = new Definition('class', array('encoderMap'));

        $container->setDefinition('security.encoder_factory.generic', $definition);

        $injector = new InjectParametersIntoEncoderFactoryPass();
        $injector->process($container);

        $expectedArguments = array(
            '%security.encoder.digest.class%',
            array(
                '%fos_user.encoder.algorithm%',
                '%fos_user.encoder.encode_as_base64%',
                '%fos_user.encoder.iterations%',
            ),
            'encoderMap',
        );

        $this->assertEquals($expectedArguments, $definition->getArguments());
    }
}