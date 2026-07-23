<?php

declare(strict_types=1);

namespace Looker\Form\Plugin\Factory;

use Laminas\Escaper\Escaper;
use Laminas\Escaper\EscaperInterface;
use Looker\Form\Plugin\MultiCheckBox;
use Looker\HTML\AttributeNormaliser;
use Looker\Plugin\Factory\DefaultDoctype;
use Looker\Plugin\HtmlAttributes;
use Looker\PluginManager;
use Psr\Container\ContainerInterface;

final class MultiCheckBoxFactory
{
    public function __invoke(ContainerInterface $container): MultiCheckBox
    {
        $escaper = $container->has(EscaperInterface::class)
            ? $container->get(EscaperInterface::class)
            : new Escaper();

        $plugins = $container->get(PluginManager::class);

        return new MultiCheckBox(
            $escaper,
            $plugins->get(HtmlAttributes::class),
            $container->get(AttributeNormaliser::class),
            DefaultDoctype::retrieve($container),
        );
    }
}
