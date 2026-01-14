<?php

declare(strict_types=1);

namespace Looker\Form\Test\Plugin;

use Laminas\Escaper\Escaper;
use Laminas\Form\Element\Textarea as Element;
use Looker\Form\Plugin\InvalidElementAttributeHandler;
use Looker\Form\Plugin\Textarea;
use Looker\Plugin\HtmlAttributes;
use Override;
use PHPUnit\Framework\TestCase;

final class TextareaTest extends TestCase
{
    private Textarea $helper;

    #[Override]
    protected function setUp(): void
    {
        $escaper      = new Escaper();
        $this->helper = new Textarea(
            $escaper,
            new HtmlAttributes($escaper),
            new InvalidElementAttributeHandler(),
        );
    }

    public function testElementIsRenderedWithElementAttributes(): void
    {
        $element = new Element('foo');
        $element->setAttribute('rows', 5);
        $element->setAttribute('cols', 6);

        self::assertSame(
            '<textarea cols="6" name="foo" rows="5"></textarea>',
            $this->helper->__invoke($element),
        );
    }

    public function testElementAttributesCanBeOverridden(): void
    {
        $element = new Element('foo');
        $element->setAttribute('rows', 5);
        $element->setAttribute('cols', 6);

        self::assertSame(
            '<textarea cols="12" name="foo" rows="5"></textarea>',
            $this->helper->__invoke($element, ['cols' => '12']),
        );
    }

    public function testInvalidAttributesAreIgnored(): void
    {
        $element = new Element('foo');

        self::assertSame(
            '<textarea name="foo"></textarea>',
            $this->helper->__invoke($element, ['goats' => '12']),
        );
    }

    public function testGlobalAttributesAreIncluded(): void
    {
        $element = new Element('foo');

        self::assertSame(
            '<textarea lang="en" name="foo"></textarea>',
            $this->helper->__invoke($element, ['lang' => 'en']),
        );
    }

    public function testBooleanTrueAttributesAreSimplified(): void
    {
        $element = new Element('foo');

        self::assertSame(
            '<textarea name="foo" readonly></textarea>',
            $this->helper->__invoke($element, ['readonly' => true]),
        );
    }

    public function testBooleanFalseAttributesAreOmitted(): void
    {
        $element = new Element('foo');

        self::assertSame(
            '<textarea name="foo"></textarea>',
            $this->helper->__invoke($element, ['readonly' => false]),
        );
    }

    public function testTextareaSpecificAttributesAreIncluded(): void
    {
        $element = new Element('foo');

        self::assertSame(
            '<textarea name="foo" wrap="hard"></textarea>',
            $this->helper->__invoke($element, ['wrap' => 'hard']),
        );
    }

    public function testTheElementIsRenderedWithoutAName(): void
    {
        $element = new Element();
        $markup  = $this->helper->__invoke($element);
        self::assertSame('<textarea ></textarea>', $markup);
    }

    public function testValueIsEscaped(): void
    {
        $element = new Element('foo');
        $element->setValue('Goats & Boats');
        self::assertSame(
            '<textarea name="foo">Goats &amp; Boats</textarea>',
            $this->helper->__invoke($element),
        );
    }
}
