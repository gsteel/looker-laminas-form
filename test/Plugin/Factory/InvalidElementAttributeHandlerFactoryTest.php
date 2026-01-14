<?php

declare(strict_types=1);

namespace Looker\Form\Test\Plugin\Factory;

use Laminas\Form\Element\Text;
use Looker\Form\Plugin\Factory\InvalidElementAttributeHandlerFactory;
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
}
