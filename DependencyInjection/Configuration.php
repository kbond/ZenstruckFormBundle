<?php

namespace Zenstruck\Bundle\FormBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('zenstruck_form');

        // Keep compatibility with symfony/config < 4.2
        if (\method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            $rootNode = $treeBuilder->root('zenstruck_form');
        }

        $rootNode
            ->children()
                ->arrayNode('form_types')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('help')->defaultFalse()->end()
                        ->booleanNode('group')->defaultFalse()->end()
                        ->booleanNode('theme')->defaultFalse()->end()
                        ->booleanNode('tunnel_entity')->defaultFalse()->end()
                        ->booleanNode('ajax_entity')->defaultFalse()->end()
                        ->booleanNode('ajax_entity_controller')->defaultFalse()->end()
                    ->end()
                ->end()
                ->variableNode('theme_options')->defaultValue(array())->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
