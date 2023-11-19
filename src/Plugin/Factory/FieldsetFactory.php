<?php

declare(strict_types=1);

namespace Looker\Form\Plugin\Factory;

use Laminas\Escaper\Escaper;
use Looker\Form\Plugin\Fieldset;
use Looker\Form\Plugin\FormElementRow;
use Looker\Plugin\HtmlAttributes;
use Looker\PluginManager;
use Psr\Container\ContainerInterface;

final class FieldsetFactory
{
    public function __invoke(ContainerInterface $container): Fieldset
    {
        $plugins = $container->get(PluginManager::class);
        $escaper = $container->has(Escaper::class)
            ? $container->get(Escaper::class)
            : new Escaper();

        return new Fieldset(
            $escaper,
            $plugins->get(HtmlAttributes::class),
            $plugins->get(FormElementRow::class),
        );
    }
}
