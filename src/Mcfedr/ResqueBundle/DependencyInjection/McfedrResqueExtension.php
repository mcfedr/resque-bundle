<?php

namespace Mcfedr\ResqueBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class McfedrResqueExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('mcfedr_resque.host', $config['host']);
        $container->setParameter('mcfedr_resque.port', $config['port']);
        $container->setParameter('mcfedr_resque.default_queue', $config['default_queue']);
        $container->setParameter('mcfedr_resque.debug', $config['debug']);
        if (array_key_exists('prefix', $config)) {
            $container->setParameter('mcfedr_resque.prefix', $config['prefix']);
        }
        $container->setParameter('mcfedr_resque.track_status', $config['track_status']);
    }
}
