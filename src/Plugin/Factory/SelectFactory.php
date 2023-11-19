<?php

declare(strict_types=1);

namespace Looker\Form\Plugin\Factory;

use Laminas\Escaper\Escaper;
use Looker\Form\Plugin\InvalidElementAttributeHandler;
use Looker\Form\Plugin\Option;
use Looker\Form\Plugin\Select;
use Looker\Plugin\HtmlAttributes;
use Looker\PluginManager;
use Psr\Container\ContainerInterface;

final class SelectFactory
{
    public function __invoke(ContainerInterface $container): Select
    {
        $escaper = $container->has(Escaper::class)
            ? $container->get(Escaper::class)
            : new Escaper();

        $plugins = $container->get(PluginManager::class);

        return new Select(
            $escaper,
            $plugins->get(HtmlAttributes::class),
            $plugins->get(Option::class),
            $plugins->get(InvalidElementAttributeHandler::class),
        );
    }
}
