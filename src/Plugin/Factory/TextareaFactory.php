<?php

declare(strict_types=1);

namespace Looker\Form\Plugin\Factory;

use Laminas\Escaper\Escaper;
use Laminas\Escaper\EscaperInterface;
use Looker\Form\Plugin\InvalidElementAttributeHandler;
use Looker\Form\Plugin\Textarea;
use Looker\HTML\AttributeNormaliser;
use Looker\Plugin\HtmlAttributes;
use Looker\PluginManager;
use Psr\Container\ContainerInterface;

final class TextareaFactory
{
    public function __invoke(ContainerInterface $container): Textarea
    {
        $escaper = $container->has(EscaperInterface::class)
            ? $container->get(EscaperInterface::class)
            : new Escaper();

        $plugins = $container->get(PluginManager::class);

        return new Textarea(
            $escaper,
            $plugins->get(HtmlAttributes::class),
            $container->get(AttributeNormaliser::class),
            $plugins->get(InvalidElementAttributeHandler::class),
        );
    }
}
