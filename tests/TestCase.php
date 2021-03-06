<?php

/*
 * This file is part of php-cache\cache-bundle package.
 *
 * (c) 2015-2015 Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Cache\CacheBundle\Tests;

use Cache\CacheBundle\DependencyInjection\CacheExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

/**
 * Class TestCase
 *
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class TestCase extends \PHPUnit_Framework_TestCase
{

    /**
     * @param ContainerBuilder $container
     * @param string           $file
     */
    protected function loadFromFile(ContainerBuilder $container, $file)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/Fixtures'));
        $loader->load($file . '.yml');
    }

    /**
     * @param array $data
     *
     * @return ContainerBuilder
     */
    protected function createContainer(array $data = array())
    {
        return new ContainerBuilder(new ParameterBag(array_merge(
            array(
                'kernel.bundles'     => array('FrameworkBundle' => 'Symfony\\Bundle\\FrameworkBundle\\FrameworkBundle'),
                'kernel.cache_dir'   => __DIR__,
                'kernel.debug'       => false,
                'kernel.environment' => 'test',
                'kernel.name'        => 'kernel',
                'kernel.root_dir'    => __DIR__,
            ),
            $data
        )));
    }

    /**
     * @param string $file
     * @param array  $data
     *
     * @return ContainerBuilder
     */
    protected function createContainerFromFile($file, $data = array())
    {
        $container = $this->createContainer($data);
        $container->registerExtension(new CacheExtension());
        $this->loadFromFile($container, $file);

        $container->getCompilerPassConfig()
            ->setOptimizationPasses(array());
        $container->getCompilerPassConfig()
            ->setRemovingPasses(array());
        $container->compile();

        return $container;
    }
}
