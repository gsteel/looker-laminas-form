<?php

declare(strict_types=1);

namespace Looker\Form\Plugin\Factory;

use Looker\Form\Plugin\CheckBox;
use Looker\Form\Plugin\InvalidElementAttributeHandler;
use Looker\HTML\AttributeNormaliser;
use Looker\Plugin\Factory\DefaultDoctype;
use Looker\Plugin\HtmlAttributes;
use Looker\PluginManager;
use Psr\Container\ContainerInterface;

final class CheckBoxFactory
{
    public function __invoke(ContainerInterface $container): CheckBox
    {
        $plugins = $container->get(PluginManager::class);

        return new CheckBox(
            $plugins->get(HtmlAttributes::class),
            $container->get(AttributeNormaliser::class),
            $plugins->get(InvalidElementAttributeHandler::class),
            DefaultDoctype::retrieve($container),
        );
    }
}
