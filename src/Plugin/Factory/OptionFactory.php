<?php

declare(strict_types=1);

namespace Looker\Form\Plugin\Factory;

use Laminas\Escaper\Escaper;
use Laminas\Escaper\EscaperInterface;
use Looker\Form\Plugin\Option;
use Looker\Plugin\HtmlAttributes;
use Looker\PluginManager;
use Psr\Container\ContainerInterface;

final class OptionFactory
{
    public function __invoke(ContainerInterface $container): Option
    {
        $escaper = $container->has(EscaperInterface::class)
            ? $container->get(EscaperInterface::class)
            : new Escaper();

        $plugins = $container->get(PluginManager::class);

        return new Option($escaper, $plugins->get(HtmlAttributes::class));
    }
}
