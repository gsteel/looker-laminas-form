<?php

declare(strict_types=1);

namespace Looker\Form\Test\Plugin\Factory;

use Laminas\Escaper\Escaper;
use Looker\Form\Plugin\ElementErrorList;
use Looker\Form\Plugin\Factory\ElementErrorListFactory;
use Looker\Form\Test\InMemoryContainer;
use Looker\Plugin\HtmlAttributes;
use Looker\PluginManager;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class ElementErrorListFactoryTest extends TestCase
{
    protected function setUp(): void
    {
    }

    /** @return array<string, array{0: ContainerInterface}> */
    public static function variousContainerSetups(): array
    {
        return [
            'Missing Escaper' => [
                new InMemoryContainer([
                    'config' => [],
                    PluginManager::class => new InMemoryContainer([
                        HtmlAttributes::class => new HtmlAttributes(new Escaper()),
                    ]),
                ]),
            ],
            'Escaper Present, missing config' => [
                new InMemoryContainer([
                    Escaper::class => new Escaper(),
                    'config' => [],
                    PluginManager::class => new InMemoryContainer([
                        HtmlAttributes::class => new HtmlAttributes(new Escaper()),
                    ]),
                ]),
            ],
            'Escaper Present, custom attributes' => [
                new InMemoryContainer([
                    Escaper::class => new Escaper(),
                    'config' => [
                        'looker' => [
                            'pluginConfig' => [
                                'formElementErrorListAttributes' => ['class' => 'muppets'],
                            ],
                        ],
                    ],
                    PluginManager::class => new InMemoryContainer([
                        HtmlAttributes::class => new HtmlAttributes(new Escaper()),
                    ]),
                ]),
            ],
        ];
    }

    #[DataProvider('variousContainerSetups')]
    public function testFactory(ContainerInterface $container): void
    {
        self::assertInstanceOf(
            ElementErrorList::class,
            (new ElementErrorListFactory())->__invoke($container),
        );
    }
}
