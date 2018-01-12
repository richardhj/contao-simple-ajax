<?php
/**
 * SimpleAjax extension for Contao Open Source CMS
 *
 * Copyright (c) 2016-2018 Richard Henkenjohann
 *
 * @package SimpleAjax
 * @author  Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 */


namespace SimpleAjax\DependencyInjection;


use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class SimpleAjaxExtension extends Extension
{

    /**
     * The files to load.
     *
     * @var string[]
     */
    private $files = [
        'services.yml',
    ];

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        foreach ($this->files as $file) {
            $loader->load($file);
        }
    }
}
