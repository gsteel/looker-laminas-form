<?php

declare(strict_types=1);

namespace Looker\Form\Plugin\Factory;

use Looker\Form\Plugin\InvalidElementAttributeHandler;
use Psr\Container\ContainerInterface;

use function Psl\Type\mixed;
use function Psl\Type\optional;
use function Psl\Type\shape;
use function Psl\Type\vec;

/** @psalm-import-type CallableSpec from InvalidElementAttributeHandler */
final class InvalidElementAttributeHandlerFactory
{
    public function __invoke(ContainerInterface $container): InvalidElementAttributeHandler
    {
        $config = shape([
            'looker' => optional(shape([
                'pluginConfig' => optional(shape([
                    'invalidElementAttributeHandlers' => optional(vec(mixed())),
                ], true)),
            ], true)),
        ], true)->assert($container->has('config') ? $container->get('config') : []);

        /**
         * Forcing this type - it cannot reasonably be verified
         * @psalm-var list<CallableSpec> $list
         */
        $list = $config['looker']['pluginConfig']['invalidElementAttributeHandlers'] ?? [];

        return new InvalidElementAttributeHandler(...$list);
    }
}
