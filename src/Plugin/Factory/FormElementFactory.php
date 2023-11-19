<?php

declare(strict_types=1);

namespace Looker\Form\Plugin\Factory;

use Looker\Form\Plugin\FormElement;
use Looker\PluginManager;
use Psr\Container\ContainerInterface;

final class FormElementFactory
{
    public function __invoke(ContainerInterface $container): FormElement
    {
        return new FormElement($container->get(PluginManager::class));
    }
}
