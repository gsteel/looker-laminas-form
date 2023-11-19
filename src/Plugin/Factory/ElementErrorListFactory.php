<?php

declare(strict_types=1);

namespace Looker\Form\Plugin\Factory;

use GSteel\Dot;
use Laminas\Escaper\Escaper;
use Looker\Form\Plugin\ElementErrorList;
use Looker\Plugin\HtmlAttributes;
use Looker\PluginManager;
use Psr\Container\ContainerInterface;
use Webmozart\Assert\Assert;

final class ElementErrorListFactory
{
    public function __invoke(ContainerInterface $container): ElementErrorList
    {
        $plugins = $container->get(PluginManager::class);
        $escaper = $container->has(Escaper::class)
            ? $container->get(Escaper::class)
            : new Escaper();

        $config = $container->get('config');
        Assert::isArray($config);
        /** @psalm-var array<string, scalar|null> $defaultAttributes */
        $defaultAttributes = Dot::arrayDefault('looker.pluginConfig.formElementErrorListAttributes', $config, []);

        return new ElementErrorList(
            $escaper,
            $plugins->get(HtmlAttributes::class),
            $defaultAttributes,
        );
    }
}
