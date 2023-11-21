<?php

declare(strict_types=1);

namespace Looker\Form\Test\Plugin;

use Laminas\Escaper\Escaper;
use Laminas\Form\Element;
use Laminas\Form\ElementInterface;
use Looker\Form\Plugin\Exception\FormElementsMustBeNamed;
use Looker\Form\Plugin\Exception\InvalidElementType;
use Looker\Form\Plugin\FormInput;
use Looker\Form\Plugin\InvalidElementAttributeHandler;
use Looker\Plugin\HtmlAttributes;
use Looker\Value\Doctype;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class FormInputTest extends TestCase
{
    private FormInput $helper;

    protected function setUp(): void
    {
        $this->helper = new FormInput(
            Doctype::HTML5,
            new HtmlAttributes(new Escaper()),
            new InvalidElementAttributeHandler(),
        );
    }

    /** @return list<array{0: ElementInterface, 1: string}> */
    public static function elementProvider(): array
    {
        return [
            [new Element\Color('foo'), '<input name="foo" type="color" value="">'],
            [new Element\Date('foo'), '<input name="foo" type="date" value="">'],
            [new Element\DateTimeLocal('foo'), '<input name="foo" type="datetime-local" value="">'],
            [new Element\Email('foo'), '<input name="foo" type="email" value="">'],
            [new Element\File('foo'), '<input name="foo" type="file" value="">'],
            [new Element\Hidden('foo'), '<input name="foo" type="hidden" value="">'],
            [new Element\Image('foo'), '<input name="foo" type="image" value="">'],
            [new Element\Month('foo'), '<input name="foo" type="month" value="">'],
            [new Element\Number('foo'), '<input name="foo" type="number" value="">'],
            [new Element\Password('foo'), '<input name="foo" type="password">'],
            [new Element\Radio('foo'), '<input name="foo" type="radio" value="">'],
            [new Element\Range('foo'), '<input name="foo" type="range" value="">'],
            [new Element\Search('foo'), '<input name="foo" type="search" value="">'],
            [new Element\Submit('foo'), '<input name="foo" type="submit" value="">'],
            [new Element\Tel('foo'), '<input name="foo" type="tel" value="">'],
            [new Element\Text('foo'), '<input name="foo" type="text" value="">'],
            [new Element\Time('foo'), '<input name="foo" type="time" value="">'],
            [new Element\Url('foo'), '<input name="foo" type="url" value="">'],
            [new Element\Week('foo'), '<input name="foo" type="week" value="">'],
        ];
    }

    #[DataProvider('elementProvider')]
    public function testRenderOfBasicElement(ElementInterface $element, string $expect): void
    {
        self::assertEquals(
            $expect,
            $this->helper->__invoke($element),
        );
    }

    public function testThatAMissingNameIsExceptional(): void
    {
        $this->expectException(FormElementsMustBeNamed::class);
        $this->helper->__invoke(new Element\Text());
    }

    public function testThatFormInputOnlyAcceptsInputTypes(): void
    {
        $this->expectException(InvalidElementType::class);
        $this->helper->__invoke(new Element\Select('foo'));
    }

    public function testThatAttributesGivenOverrideElementAttributes(): void
    {
        $element = new Element\Text('foo');
        $element->setAttribute('maxlength', '10');

        self::assertEquals(
            '<input maxlength="20" name="foo" type="text" value="">',
            $this->helper->__invoke($element, ['maxlength' => '20']),
        );
    }

    public function testXhtmlDoctypeWillAlterClosingTag(): void
    {
        $helper = new FormInput(
            Doctype::XHTML1Strict,
            new HtmlAttributes(new Escaper()),
            new InvalidElementAttributeHandler(),
        );

        self::assertSame(
            '<input name="foo" type="text" value="" />',
            $helper(new Element\Text('foo')),
        );
    }

    public function testFalseBooleanAttributesAreOmitted(): void
    {
        self::assertSame(
            '<input name="foo" type="text" value="">',
            ($this->helper)(new Element\Text('foo'), ['readonly' => false]),
        );
    }

    public function testTrueBooleanAttributesAreSimplified(): void
    {
        self::assertSame(
            '<input name="foo" readonly type="text" value="">',
            ($this->helper)(new Element\Text('foo'), ['readonly' => true]),
        );
    }

    public function testGlobalAttributesAreAccepted(): void
    {
        self::assertSame(
            '<input id="baz" name="foo" type="text" value="">',
            ($this->helper)(new Element\Text('foo'), ['id' => 'baz']),
        );
    }
}
