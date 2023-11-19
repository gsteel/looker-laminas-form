<?php

declare(strict_types=1);

namespace Looker\Form\Test\Plugin;

use Laminas\Form\Element\Text;
use Looker\Form\Plugin\InvalidElementAttributeHandler;
use PHPUnit\Framework\TestCase;

class InvalidElementAttributeHandlerTest extends TestCase
{
    public function testThatByDefaultAriaInvalidIsAddedToAttributes(): void
    {
        $element = new Text();
        $element->setMessages(['Bad News']);

        $attributes = (new InvalidElementAttributeHandler())->__invoke($element, []);

        self::assertSame(['aria-invalid' => 'true'], $attributes);
    }

    public function testThatHandlersAreOverriddenByConstructorArgs(): void
    {
        $element = new Text();
        $element->setMessages(['Bad News']);

        /**
         * @param array<string, scalar|null> $attributes
         *
         * @return array<string, scalar|null>
         *
         * @psalm-var callable(array<string, scalar|null>): array<string, scalar|null> $handler
         */
        $handler = static function (
            array $attributes,
        ): array {
            $attributes['class'] = 'error';

            return $attributes;
        };

        $attributes = (new InvalidElementAttributeHandler($handler))->__invoke($element, []);
        self::assertSame(['class' => 'error'], $attributes);
    }

    public function testThatAttributesAreNotModifiedWhenTheElementIsNotInAnErrorState(): void
    {
        $attributes = (new InvalidElementAttributeHandler())->__invoke(new Text(), []);
        self::assertSame([], $attributes);
    }
}
