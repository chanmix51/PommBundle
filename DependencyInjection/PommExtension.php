<?php

namespace Pomm\PommBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class PommExtension implements ExtensionInterface
{
    /**
     * Loads the bundle configuration.
     *
     * @param array $config    An array of configuration settings
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);

        $this->loadDefaults($container);

        foreach ($config['databases'] as $name => $parameters) {
            $id = sprintf('pomm.%s_database', $name);
            $databaseDefinition = $container->setDefinition(
                $id,
                new Definition($parameters['class'], array($parameters))
            );
            foreach ($parameters['converters'] as $type => $converter) {
                $converterDefinition = new Definition($converter['class']);
                $databaseDefinition->addMethodCall('registerConverter', array($type, $converterDefinition, $converter['types']));
            }
            $container->getDefinition('pomm')->addMethodCall('setDatabase', array($name, new Reference($id)));
        }
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

