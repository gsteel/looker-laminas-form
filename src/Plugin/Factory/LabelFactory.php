<?php

declare(strict_types=1);

namespace Looker\Form\Plugin\Factory;

use Laminas\Escaper\Escaper;
use Looker\Form\Plugin\Label;
use Looker\Plugin\HtmlAttributes;
use Looker\PluginManager;
use Psr\Container\ContainerInterface;

final class LabelFactory
{
    public function __invoke(ContainerInterface $container): Label
    {
        $escaper = $container->has(Escaper::class)
            ? $container->get(Escaper::class)
            : new Escaper();

        $plugins = $container->get(PluginManager::class);

        return new Label($escaper, $plugins->get(HtmlAttributes::class));
    }
}
