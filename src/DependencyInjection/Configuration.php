<?php

namespace TBoileau\UploadBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package TBoileau\UploadBundle\DependencyInjection
 * @author Thomas Boileau <t-boileau@email.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root("t_boileau_upload");

        $rootNode
            ->children()
                ->scalarNode('upload_dir')->end()
                ->scalarNode('web_path')->end()
            ->end()
        ;

        return $treeBuilder;
    }

}