<?php

namespace GHub\Bundle\PommBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

class PommExtension extends Extension
{
    /**
     * Loads the bundle configuration.
     *
     * @param array $config    An array of configuration settings
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    public function load(array $config, ContainerBuilder $container)
    {
        if (!$container->hasDefinition('pomm')) {
            $this->loadDefaults($container);
        }

        $configuration = $config[0];

        if (!array_key_exists('connections', $configuration) or !is_array($configuration['connections'])) {
            throw new InvalidArgumentException("Inexistant or invalid connections definition");
        }

        $container->setParameter('pomm.connections', $configuration['connections']);
    }

    /**
     * Load defaults settings
     *
     * @param ContainerBuilder container
     */
    protected function loadDefaults(ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('pomm.yml');
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\DependencyInjection\Extension.ExtensionInterface::getXsdValidationBasePath()
     */
    public function getXsdValidationBasePath()
    {
        return null;
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\DependencyInjection\Extension.ExtensionInterface::getNamespace()
     */
    public function getNamespace()
    {
        return '';
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\DependencyInjection\Extension.ExtensionInterface::getAlias()
     */
    public function getAlias()
    {
        return 'pomm';
    }
}

