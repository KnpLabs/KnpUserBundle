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
            $container->getParameter('security.encoder.digest.class'),
            $container->getParameter('fos_user.encoder.encode_hash_as_base64'),
            $container->getParameter('fos_user.encoder.iterations'),
            reset($arguments),
        );
        $definition->setArguments($newArgs);
    }
}