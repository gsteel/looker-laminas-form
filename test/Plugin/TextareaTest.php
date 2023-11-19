<?php

declare(strict_types=1);

namespace Looker\Form\Test\Plugin;

use Laminas\Escaper\Escaper;
use Laminas\Form\Element\Textarea as Element;
use Looker\Form\Plugin\Exception\FormElementsMustBeNamed;
use Looker\Form\Plugin\InvalidElementAttributeHandler;
use Looker\Form\Plugin\Textarea;
use Looker\Plugin\HtmlAttributes;
use PHPUnit\Framework\TestCase;

class TextareaTest extends TestCase
{
    private Textarea $helper;

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

        self::assertEquals(
            '<textarea cols="6" name="foo" rows="5"></textarea>',
            $this->helper->__invoke($element),
        );
    }

    public function testElementAttributesCanBeOverridden(): void
    {
        $element = new Element('foo');
        $element->setAttribute('rows', 5);
        $element->setAttribute('cols', 6);

        self::assertEquals(
            '<textarea cols="12" name="foo" rows="5"></textarea>',
            $this->helper->__invoke($element, ['cols' => '12']),
        );
    }

    public function testInvalidAttributesAreIgnored(): void
    {
        $element = new Element('foo');

        self::assertEquals(
            '<textarea name="foo"></textarea>',
            $this->helper->__invoke($element, ['goats' => '12']),
        );
    }

    public function testGlobalAttributesAreIncluded(): void
    {
        $element = new Element('foo');

        self::assertEquals(
            '<textarea lang="en" name="foo"></textarea>',
            $this->helper->__invoke($element, ['lang' => 'en']),
        );
    }

    public function testBooleanTrueAttributesAreSimplified(): void
    {
        $element = new Element('foo');

        self::assertEquals(
            '<textarea name="foo" readonly></textarea>',
            $this->helper->__invoke($element, ['readonly' => true]),
        );
    }

    public function testBooleanFalseAttributesAreOmitted(): void
    {
        $element = new Element('foo');

        self::assertEquals(
            '<textarea name="foo"></textarea>',
            $this->helper->__invoke($element, ['readonly' => false]),
        );
    }

    public function testTextareaSpecificAttributesAreIncluded(): void
    {
        $element = new Element('foo');

        self::assertEquals(
            '<textarea name="foo" wrap="hard"></textarea>',
            $this->helper->__invoke($element, ['wrap' => 'hard']),
        );
    }

    public function testANameIsRequired(): void
    {
        $element = new Element();
        $this->expectException(FormElementsMustBeNamed::class);
        $this->helper->__invoke($element);
    }

    public function testValueIsEscaped(): void
    {
        $element = new Element('foo');
        $element->setValue('Goats & Boats');
        self::assertEquals(
            '<textarea name="foo">Goats &amp; Boats</textarea>',
            $this->helper->__invoke($element),
        );
    }
}
