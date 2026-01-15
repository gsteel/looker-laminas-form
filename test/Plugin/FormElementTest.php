<?php

declare(strict_types=1);

namespace Looker\Form\Test\Plugin;

use Laminas\Form\Element;
use Laminas\Form\ElementInterface;
use Laminas\Form\Fieldset;
use Laminas\Form\Form;
use Looker\Form\Plugin\Button;
use Looker\Form\Plugin\CheckBox;
use Looker\Form\Plugin\Fieldset as FieldsetPlugin;
use Looker\Form\Plugin\Form as FormPlugin;
use Looker\Form\Plugin\FormElement;
use Looker\Form\Plugin\FormInput;
use Looker\Form\Plugin\MultiCheckBox;
use Looker\Form\Plugin\Select;
use Looker\Form\Plugin\Textarea;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

final class FormElementTest extends TestCase
{
    /** @return list<array{0: ElementInterface, 1: class-string}> */
    public static function elementProvider(): array
    {
        return [
            [new Element\Button('foo'), Button::class],
            [new Element\Checkbox('foo'), CheckBox::class],
            [new Element\Color('foo'), FormInput::class],
            [new Element\Date('foo'), FormInput::class],
            [new Element\DateTimeLocal('foo'), FormInput::class],
            [new Element\Email('foo'), FormInput::class],
            [new Element\File('foo'), FormInput::class],
            [new Element\Hidden('foo'), FormInput::class],
            [new Element\Image('foo'), FormInput::class],
            [new Element\Month('foo'), FormInput::class],
            [new Element\Number('foo'), FormInput::class],
            [new Element\Password('foo'), FormInput::class],
            [new Element\Range('foo'), FormInput::class],
            [new Element\Search('foo'), FormInput::class],
            [new Element\Submit('foo'), FormInput::class],
            [new Element\Tel('foo'), FormInput::class],
            [new Element\Text('foo'), FormInput::class],
            [new Element\Time('foo'), FormInput::class],
            [new Element\Url('foo'), FormInput::class],
            [new Element\Week('foo'), FormInput::class],
            [new Element\Select('foo'), Select::class],
            [new Element\Textarea('foo'), Textarea::class],
            [new Form('foo'), FormPlugin::class],
            [new Fieldset('foo'), FieldsetPlugin::class],
            [new Element\MultiCheckbox('foo'), MultiCheckBox::class],
            [new Element\Radio('foo'), MultiCheckBox::class],
        ];
    }

    #[DataProvider('elementProvider')]
    public function testExpectedPluginIsExecuted(
        ElementInterface $element,
        string $expectedPlugin,
    ): void {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())
            ->method('get')
            ->with($expectedPlugin)
            ->willReturn(static fn (): string => 'string');

        $plugin = new FormElement($container);

        self::assertSame('string', $plugin->__invoke($element));
    }
}
