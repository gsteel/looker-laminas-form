<?php

declare(strict_types=1);

namespace Looker\Form\Plugin\Factory;

use Laminas\Escaper\Escaper;
use Laminas\Escaper\EscaperInterface;
use Looker\Form\Plugin\Label;
use Looker\Plugin\HtmlAttributes;
use Looker\PluginManager;
use Psr\Container\ContainerInterface;

final class LabelFactory
{
    public function __invoke(ContainerInterface $container): Label
    {
        $escaper = $container->has(EscaperInterface::class)
            ? $container->get(EscaperInterface::class)
            : new Escaper();

        $plugins = $container->get(PluginManager::class);

        return new Label($escaper, $plugins->get(HtmlAttributes::class));
    }
}
