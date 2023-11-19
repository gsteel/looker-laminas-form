<?php

declare(strict_types=1);

namespace Looker\Form\Test;

use Laminas\Escaper\Escaper;
use Laminas\ServiceManager\ServiceManager;
use Looker\Form\ConfigProvider;
use Looker\Plugin\HtmlAttributes;
use Looker\PluginManager as PluginManagerInterface;
use Psr\Container\ContainerInterface;

use function PHPUnit\Framework\assertIsArray;

/** @psalm-import-type ServiceManagerConfiguration from ServiceManager */
final class PluginManagerSetup
{
    public static function getContainer(): ContainerInterface
    {
        $config = [
            'services' => [
                Escaper::class => new Escaper(),
            ],
        ];

        $container = new ServiceManager($config);
        $container->setService(PluginManagerInterface::class, self::buildPluginManager($container));
        $container->setService('config', (new ConfigProvider())->__invoke());

        return $container;
    }

    private static function buildPluginManager(ContainerInterface $container): PluginManager
    {
        $config = (new ConfigProvider())->__invoke()['looker'] ?? [];
        assertIsArray($config);
        /** @psalm-var ServiceManagerConfiguration $pluginConfig */
        $pluginConfig             = $config['plugins'] ?? [];
        $pluginConfig['services'] = [
            HtmlAttributes::class => new HtmlAttributes(new Escaper()),
        ];

        return new PluginManager($container, $pluginConfig);
    }
}
