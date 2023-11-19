<?php

declare(strict_types=1);

namespace Looker\Form\Plugin\Factory;

use Laminas\Escaper\Escaper;
use Looker\Form\Plugin\ElementErrorList;
use Looker\Form\Plugin\FormElement;
use Looker\Form\Plugin\FormElementRow;
use Looker\Form\Plugin\Label;
use Looker\Plugin\HtmlAttributes;
use Looker\PluginManager;
use Psr\Container\ContainerInterface;

final class FormElementRowFactory
{
    public function __invoke(ContainerInterface $container): FormElementRow
    {
        $plugins = $container->get(PluginManager::class);
        $escaper = $container->has(Escaper::class)
            ? $container->get(Escaper::class)
            : new Escaper();

        return new FormElementRow(
            $escaper,
            $plugins->get(Label::class),
            $plugins->get(ElementErrorList::class),
            $plugins->get(FormElement::class),
            $plugins->get(HtmlAttributes::class),
        );
    }
}
