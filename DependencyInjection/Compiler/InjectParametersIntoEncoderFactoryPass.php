<?php

namespace Bundle\FOS\UserBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class InjectParametersIntoEncoderFactoryPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('security.encoder_factory.generic')) {
            return;
        }

        $definition = $container->getDefinition('security.encoder_factory.generic');
        $arguments = $definition->getArguments();
        $newArgs = array(
            '%security.encoder.digest.class%',
            array(
                '%fos_user.encoder.algorithm%',
                '%fos_user.encoder.encode_as_base64%',
                '%fos_user.encoder.iterations%',
            ),
            reset($arguments),
        );
        $definition->setArguments($newArgs);
    }
}