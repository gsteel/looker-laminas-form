<?php

declare(strict_types=1);

namespace Looker\Form\Plugin\Factory;

use Laminas\Escaper\Escaper;
use Looker\Form\Plugin\InvalidElementAttributeHandler;
use Looker\Form\Plugin\Textarea;
use Looker\Plugin\HtmlAttributes;
use Looker\PluginManager;
use Psr\Container\ContainerInterface;

final class TextareaFactory
{
    public function __invoke(ContainerInterface $container): Textarea
    {
        $escaper = $container->has(Escaper::class)
            ? $container->get(Escaper::class)
            : new Escaper();

        $plugins = $container->get(PluginManager::class);

        return new Textarea(
            $escaper,
            $plugins->get(HtmlAttributes::class),
            $plugins->get(InvalidElementAttributeHandler::class),
        );
    }
}
