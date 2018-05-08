<?php

namespace TBoileau\UploadBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Class TBoileauUploadExtension
 * @package TBoileau\FormHandlerBundle\DependencyInjection
 * @author Thomas Boileau <t-boileau@email.com>
 */
class TBoileauUploadExtension extends Extension implements ExtensionInterface, CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load("services.yaml");

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $definition = $container->getDefinition('t_boileau_upload.handler.upload');
        $definition->addArgument($config['upload_dir']);
        $definition->addArgument($config['web_path']);
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $twigFilesystemLoaderDefinition = $container->getDefinition('twig.loader.native_filesystem');
        $twigFilesystemLoaderDefinition->addMethodCall('addPath', [__DIR__.'/../Resources/views']);

        $resources = [];
        if ($container->hasParameter('twig.form.resources')) {
            $resources = $container->getParameter('twig.form.resources');
        }
        $resources[] = 'form.html.twig';
        $container->setParameter('twig.form.resources', $resources);
    }

}