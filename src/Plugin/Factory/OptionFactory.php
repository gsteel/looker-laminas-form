<?php

declare(strict_types=1);

namespace Looker\Form\Plugin\Factory;

use Laminas\Escaper\Escaper;
use Looker\Form\Plugin\Option;
use Looker\Plugin\HtmlAttributes;
use Looker\PluginManager;
use Psr\Container\ContainerInterface;

final class OptionFactory
{
    public function __invoke(ContainerInterface $container): Option
    {
        $escaper = $container->has(Escaper::class)
            ? $container->get(Escaper::class)
            : new Escaper();

        $plugins = $container->get(PluginManager::class);

        return new Option($escaper, $plugins->get(HtmlAttributes::class));
    }
}
