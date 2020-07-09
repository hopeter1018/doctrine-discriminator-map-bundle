<?php

namespace HoPeter1018\DoctrineDiscriminatorMapBundle\DependencyInjection;

use HoPeter1018\DoctrineDiscriminatorMapBundle\Annotation\DiscriminatorEntry;
use HoPeter1018\DoctrineDiscriminatorMapBundle\Annotation\DiscriminatorParent;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @see http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class HoPeter1018DoctrineDiscriminatorMapExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $this->addAnnotatedClassesToCompile([
            DiscriminatorParent::class,
            DiscriminatorEntry::class,
        ]);
    }
}
