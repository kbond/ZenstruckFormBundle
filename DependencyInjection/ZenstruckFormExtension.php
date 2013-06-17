<?php

namespace Zenstruck\Bundle\FormBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Processor;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class ZenstruckFormExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        if ($config['form_types']['datetime']) {
            $loader->load('date_type.xml');
        }

        if ($config['form_types']['help']) {
            $loader->load('help_type.xml');
        }

        if ($config['form_types']['group']) {
            if (!array_key_exists('ZenstruckSlugifyBundle', $container->getParameter('kernel.bundles'))) {
                throw new \Exception('ZenstruckSlugifyBundle must be installed in order to use the "group" type.');
            }
            $loader->load('group_type.xml');
        }

        if ($config['form_types']['ajax_entity']) {
            $loader->load('ajax_entity_type.xml');
        }

        if ($config['form_types']['ajax_entity_controller']) {
            if (!class_exists('\Zend\Crypt\BlockCipher')) {
                throw new \Exception('zendframework/zend-crypt must be installed to use the ajax_entity_controller feature.');
            }

            $loader->load('ajax_entity_controller.xml');
        }

        if ($config['form_types']['tunnel_entity']) {
            $loader->load('tunnel_entity_type.xml');
        }
    }
}
