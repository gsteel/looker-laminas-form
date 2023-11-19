<?php

declare(strict_types=1);

namespace Looker\Form\Plugin\Factory;

use Looker\Form\Plugin\FormInput;
use Looker\Form\Plugin\InvalidElementAttributeHandler;
use Looker\Plugin\Factory\DefaultDoctype;
use Looker\Plugin\HtmlAttributes;
use Looker\PluginManager;
use Psr\Container\ContainerInterface;

final class FormInputFactory
{
    public function __invoke(ContainerInterface $container): FormInput
    {
        $plugins = $container->get(PluginManager::class);

        return new FormInput(
            DefaultDoctype::retrieve($container),
            $plugins->get(HtmlAttributes::class),
            $plugins->get(InvalidElementAttributeHandler::class),
        );
    }
}
