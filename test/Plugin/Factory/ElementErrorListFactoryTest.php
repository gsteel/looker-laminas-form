<?php

declare(strict_types=1);

namespace Looker\Form\Test\Plugin\Factory;

use Laminas\Escaper\Escaper;
use Laminas\Form\Element\Text;
use Looker\Form\Plugin\ElementErrorList;
use Looker\Form\Plugin\Factory\ElementErrorListFactory;
use Looker\Form\Test\InMemoryContainer;
use Looker\Plugin\HtmlAttributes;
use Looker\PluginManager;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Throwable;

final class ElementErrorListFactoryTest extends TestCase
{
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
            'Partial Config 1' => [
                new InMemoryContainer([
                    'config' => [
                        'looker' => [],
                    ],
                    PluginManager::class => new InMemoryContainer([
                        HtmlAttributes::class => new HtmlAttributes(new Escaper()),
                    ]),
                ]),
            ],
            'Partial Config 2' => [
                new InMemoryContainer([
                    'config' => [
                        'looker' => [
                            'pluginConfig' => [],
                        ],
                    ],
                    PluginManager::class => new InMemoryContainer([
                        HtmlAttributes::class => new HtmlAttributes(new Escaper()),
                    ]),
                ]),
            ],
            'Partial Config 3' => [
                new InMemoryContainer([
                    'config' => [
                        'looker' => [
                            'pluginConfig' => [
                                'formElementErrorListAttributes' => [],
                            ],
                        ],
                    ],
                    PluginManager::class => new InMemoryContainer([
                        HtmlAttributes::class => new HtmlAttributes(new Escaper()),
                    ]),
                ]),
            ],
            'Partial Config 4' => [
                new InMemoryContainer([
                    'config' => [
                        'looker' => [],
                        'other-key' => 'foo',
                    ],
                    PluginManager::class => new InMemoryContainer([
                        HtmlAttributes::class => new HtmlAttributes(new Escaper()),
                    ]),
                ]),
            ],
            'Partial Config 5' => [
                new InMemoryContainer([
                    'config' => [
                        'looker' => [
                            'pluginConfig' => [
                                'formElementErrorListAttributes' => [],
                                'other-key' => 'foo',
                            ],
                            'other-key' => 'foo',
                        ],
                        'other-key' => 'foo',
                    ],
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

    /** @return list<array{0: array<array-key, mixed>}> */
    public static function invalidConfigProvider(): array
    {
        return [
            [['looker' => 'fred']],
            [['looker' => ['pluginConfig' => 'jenny']]],
            [['looker' => ['pluginConfig' => ['formElementErrorListAttributes' => 'maggie']]]],
            [['looker' => ['pluginConfig' => ['formElementErrorListAttributes' => ['jim' => (object) ['a' => 'b']]]]]],
        ];
    }

    /** @param array<array-key, mixed> $config */
    #[DataProvider('invalidConfigProvider')]
    public function testInvalidConfigScenarios(array $config): void
    {
        $container = new InMemoryContainer([
            Escaper::class => new Escaper(),
            'config' => $config,
            PluginManager::class => new InMemoryContainer([
                HtmlAttributes::class => new HtmlAttributes(new Escaper()),
            ]),
        ]);

        $this->expectException(Throwable::class);
        (new ElementErrorListFactory())->__invoke($container);
    }

    public function testValidConfigIsUsedForThePlugin(): void
    {
        $container = new InMemoryContainer([
            Escaper::class => new Escaper(),
            'config' => [
                'looker' => [
                    'pluginConfig' => [
                        'formElementErrorListAttributes' => ['data-foo' => 'bar'],
                    ],
                ],
            ],
            PluginManager::class => new InMemoryContainer([
                HtmlAttributes::class => new HtmlAttributes(new Escaper()),
            ]),
        ]);

        $plugin  = (new ElementErrorListFactory())->__invoke($container);
        $element = new Text('foo');
        $element->setMessages(['bad' => 'news']);
        $markup = $plugin->__invoke($element);

        self::assertStringContainsString('data-foo="bar"', $markup);
    }
}
