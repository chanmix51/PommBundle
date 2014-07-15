<?php

namespace Pomm\PommBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Pomm\PommBundle\DependencyInjection\Security\UserProvider\PommFactory;

class PommBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        if ($container->hasExtension('security')) {
            $container->getExtension('security')->addUserProviderFactory(new PommFactory('pomm', 'pomm.security.user.provider'));
        }
    }
}
