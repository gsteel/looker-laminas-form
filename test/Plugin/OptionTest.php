<?php

declare(strict_types=1);

namespace Looker\Form\Test\Plugin;

use Laminas\Escaper\Escaper;
use Laminas\Form\Element\Select;
use Looker\Form\Plugin\Exception\SelectElementCannotBeRendered;
use Looker\Form\Plugin\Option;
use Looker\Plugin\HtmlAttributes;
use PHPUnit\Framework\TestCase;

class OptionTest extends TestCase
{
    private Option $helper;
    private Select $element;

    protected function setUp(): void
    {
        $escaper       = new Escaper();
        $this->helper  = new Option(
            $escaper,
            new HtmlAttributes($escaper),
        );
        $this->element = new Select('element', [
            'value_options' => [
                'a' => 'A',
                'b' => 'B',
            ],
        ]);
    }

    public function testThatTheSelectedOptionWillHaveTheSelectedAttribute(): void
    {
        $this->element->setValue('a');

        $markup = $this->helper->__invoke($this->element, 'a', 'A', []);
        self::assertEquals('<option selected value="a">A</option>', $markup);

        $markup = $this->helper->__invoke($this->element, 'b', 'B', []);
        self::assertEquals('<option value="b">B</option>', $markup);
    }

    public function testThatAValidBooleanAttributeWillBePresentInTheMarkup(): void
    {
        $markup = $this->helper->__invoke($this->element, 'a', 'A', ['disabled' => true]);
        self::assertEquals('<option disabled value="a">A</option>', $markup);
    }

    public function testThatABooleanFalseAttributesWillNotBePresentInTheMarkup(): void
    {
        $markup = $this->helper->__invoke($this->element, 'a', 'A', ['disabled' => false]);
        self::assertEquals('<option value="a">A</option>', $markup);
    }

    public function testThatInvalidAttributesAreIgnored(): void
    {
        $markup = $this->helper->__invoke($this->element, 'a', 'A', ['goats' => 'good']);
        self::assertEquals('<option value="a">A</option>', $markup);
    }

    public function testThatGlobalAttributesAreNotIgnored(): void
    {
        $markup = $this->helper->__invoke($this->element, 'a', 'A', ['tabindex' => 5, 'id' => 'foo']);
        self::assertEquals('<option id="foo" tabindex="5" value="a">A</option>', $markup);
    }

    public function testAnArrayValueOnANonMultipleSelectIsExceptional(): void
    {
        $this->element->setValue(['foo', 'bar']);
        $this->expectException(SelectElementCannotBeRendered::class);
        $this->expectExceptionMessage('is not a multi-select and it’s selected value is not scalar');
        $this->helper->__invoke($this->element, 'a', 'A', []);
    }

    public function testANonArrayValueForAMultiSelectIsExceptional(): void
    {
        $this->element->setAttribute('multiple', true);
        $this->element->setValue((object) ['foo' => 'baz']);

        $this->expectException(SelectElementCannotBeRendered::class);
        $this->expectExceptionMessage('is a multi-select but it’s selected value is not iterable');
        $this->helper->__invoke($this->element, 'a', 'A', []);
    }

    public function testThatValuesAreCorrectlySelectedWhenTheValueIsAnArrayForAMultipleSelect(): void
    {
        $this->element->setAttribute('multiple', true);
        $this->element->setValue(['a', 'b']);

        $markup = $this->helper->__invoke($this->element, 'a', 'A', []);
        self::assertEquals('<option selected value="a">A</option>', $markup);
        $markup = $this->helper->__invoke($this->element, 'b', 'B', []);
        self::assertEquals('<option selected value="b">B</option>', $markup);
    }

    public function testThatAScalarValueForAMultipleSelectIsSelected(): void
    {
        $this->element->setAttribute('multiple', true);
        $this->element->setValue('a');

        $markup = $this->helper->__invoke($this->element, 'a', 'A', []);
        self::assertEquals('<option selected value="a">A</option>', $markup);
        $markup = $this->helper->__invoke($this->element, 'b', 'B', []);
        self::assertEquals('<option value="b">B</option>', $markup);
    }
}
