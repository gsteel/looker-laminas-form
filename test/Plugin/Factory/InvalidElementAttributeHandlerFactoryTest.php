<?php

declare(strict_types=1);

namespace Looker\Form\Test\Plugin\Factory;

use Laminas\Form\Element\Text;
use Looker\Form\Plugin\Factory\InvalidElementAttributeHandlerFactory;
use Looker\Form\Test\InMemoryContainer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Throwable;

final class InvalidElementAttributeHandlerFactoryTest extends TestCase
{
    /** @return list<array{0: array<array-key, mixed>}> */
    public static function emptyConfigScenarios(): array
    {
        return [
            [[]],
            [['looker' => []]],
            [['looker' => ['pluginConfig' => []]]],
            [['looker' => ['pluginConfig' => ['invalidElementAttributeHandlers' => []]]]],
            [
                [
                    'looker' => [
                        'pluginConfig' => [
                            'invalidElementAttributeHandlers' => [],
                            'other-key' => 'foo',
                        ],
                        'other-key' => 'foo',
                    ],
                    'other-key' => 'foo',
                ],
            ],
        ];
    }

    /** @param array<array-key, mixed> $config */
    #[DataProvider('emptyConfigScenarios')]
    public function testMissingConfigWillYieldAnEmptyList(array $config): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())
            ->method('has')
            ->with('config')
            ->willReturn(true);
        $container->expects($this->once())
            ->method('get')
            ->with('config')
            ->willReturn($config);

        $factory = new InvalidElementAttributeHandlerFactory();
        $handler = $factory->__invoke($container);
        $element = new Text();
        $element->setMessages(['bad' => 'news']);
        $attributes = $handler($element, []);
        self::assertArrayHasKey('aria-invalid', $attributes);
    }

    /** @return list<array{0: array<array-key, mixed>}> */
    public static function invalidConfigScenarios(): array
    {
        return [
            [['looker' => 'fred']],
            [['looker' => ['pluginConfig' => 'fred']]],
            [['looker' => ['pluginConfig' => ['invalidElementAttributeHandlers' => 'fred']]]],
        ];
    }

    /** @param array<array-key, mixed> $config */
    #[DataProvider('invalidConfigScenarios')]
    public function testConfigWithIncorrectTypesWillCauseExceptions(array $config): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())
            ->method('has')
            ->with('config')
            ->willReturn(true);
        $container->expects($this->once())
            ->method('get')
            ->with('config')
            ->willReturn($config);

        $factory = new InvalidElementAttributeHandlerFactory();
        $this->expectException(Throwable::class);
        $factory->__invoke($container);
    }

    public function testValidConfigIsUsedForThePlugin(): void
    {
        $container = new InMemoryContainer([
            'config' => [
                'looker' => [
                    'pluginConfig' => [
                        'invalidElementAttributeHandlers' => [
                            static function (array $attribs): array {
                                $attribs['data-baz'] = 'bing';

                                return $attribs;
                            },
                        ],
                    ],
                ],
            ],
        ]);

        $handler = new InvalidElementAttributeHandlerFactory()->__invoke($container);

        $element = new Text('foo');
        $element->setMessages(['bad' => 'news']);

        $attribs = $handler->__invoke($element, []);

        self::assertArrayHasKey('data-baz', $attribs);
        self::assertSame('bing', $attribs['data-baz']);
        self::assertArrayNotHasKey('aria-invalid', $attribs);
    }
}
