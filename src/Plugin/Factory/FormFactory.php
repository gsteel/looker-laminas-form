<?php

declare(strict_types=1);

namespace Looker\Form\Plugin\Factory;

use Looker\Form\Plugin\Form;
use Looker\Form\Plugin\FormElementRow;
use Looker\Plugin\HtmlAttributes;
use Looker\PluginManager;
use Psr\Container\ContainerInterface;

final class FormFactory
{
    public function __invoke(ContainerInterface $container): Form
    {
        $plugins = $container->get(PluginManager::class);

        return new Form(
            $plugins->get(HtmlAttributes::class),
            $plugins->get(FormElementRow::class),
        );
    }
}
