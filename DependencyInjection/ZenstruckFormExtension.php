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

        if ($config['form_types']['help']) {
            $loader->load('help_type.xml');
        }

        if ($config['form_types']['group']) {
            $bundles = $container->getParameter('kernel.bundles');
            if (!isset($bundles['ZenstruckSlugifyBundle']) && !isset($bundles['CocurSlugifyBundle'])) {
                throw new \Exception('ZenstruckSlugifyBundle or CocurSlugifyBundle must be installed in order to use the "group" type.');
            }
            $loader->load('group_type.xml');
        }

        if ($config['form_types']['theme']) {
            $container->setParameter('zenstruck_form.theme_options', $config['theme_options']);
            $loader->load('theme_type.xml');
        }

        if ($config['form_types']['ajax_entity']) {
            $loader->load('ajax_entity_type.xml');
        }

        if ($config['form_types']['ajax_entity_controller']) {
            $loader->load('ajax_entity_controller.xml');
        }

        if ($config['form_types']['tunnel_entity']) {
            $loader->load('tunnel_entity_type.xml');
        }
    }
}
