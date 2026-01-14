<?php

declare(strict_types=1);

namespace Looker\Form\Plugin\Factory;

use Laminas\Escaper\Escaper;
use Laminas\Escaper\EscaperInterface;
use Looker\Form\Plugin\ElementErrorList;
use Looker\Plugin\HtmlAttributes;
use Looker\PluginManager;
use Psr\Container\ContainerInterface;

use function Psl\Type\dict;
use function Psl\Type\optional;
use function Psl\Type\scalar;
use function Psl\Type\shape;
use function Psl\Type\string;

final class ElementErrorListFactory
{
    public function __invoke(ContainerInterface $container): ElementErrorList
    {
        $plugins = $container->get(PluginManager::class);
        $escaper = $container->has(EscaperInterface::class)
            ? $container->get(EscaperInterface::class)
            : new Escaper();

        $config = shape([
            'looker' => optional(shape([
                'pluginConfig' => optional(shape([
                    'formElementErrorListAttributes' => optional(dict(string(), scalar())),
                ], true)),
            ], true)),
        ])->assert($container->get('config'));

        $defaultAttributes = $config['looker']['pluginConfig']['formElementErrorListAttributes'] ?? [];

        return new ElementErrorList(
            $escaper,
            $plugins->get(HtmlAttributes::class),
            $defaultAttributes,
        );
    }
}
