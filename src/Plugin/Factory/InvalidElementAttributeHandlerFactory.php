<?php

declare(strict_types=1);

namespace Looker\Form\Plugin\Factory;

use GSteel\Dot;
use Looker\Form\Plugin\InvalidElementAttributeHandler;
use Psr\Container\ContainerInterface;
use Webmozart\Assert\Assert;

/** @psalm-import-type CallableSpec from InvalidElementAttributeHandler */
final class InvalidElementAttributeHandlerFactory
{
    public function __invoke(ContainerInterface $container): InvalidElementAttributeHandler
    {
        $config = $container->has('config') ? $container->get('config') : [];
        Assert::isArray($config);

        /**
         * Forcing this type - it cannot reasonably be verified
         * @psalm-var list<CallableSpec> $list
         */
        $list = Dot::arrayDefault('looker.pluginConfig.invalidElementAttributeHandlers', $config, []);

        return new InvalidElementAttributeHandler(...$list);
    }
}
