<?php

declare(strict_types=1);

namespace Looker\Form\Test\Plugin;

use Laminas\Escaper\Escaper;
use Laminas\Form\Element\Checkbox as CheckboxElement;
use Looker\Form\Plugin\CheckBox;
use Looker\Form\Plugin\Exception\FormElementsMustBeNamed;
use Looker\Form\Plugin\InvalidElementAttributeHandler;
use Looker\Plugin\HtmlAttributes;
use Looker\Value\Doctype;
use PHPUnit\Framework\TestCase;

class CheckBoxTest extends TestCase
{
    private CheckBox $plugin;

    protected function setUp(): void
    {
        $this->plugin = new CheckBox(
            new HtmlAttributes(
                new Escaper(),
            ),
            new InvalidElementAttributeHandler(),
            Doctype::HTML5,
        );
    }

    public function testBasicOutput(): void
    {
        $element = new CheckboxElement('foo');

        self::assertSame(
            <<<'HTML'
            <input name="foo" type="hidden" value="0">
            <input name="foo" type="checkbox" value="1">
            HTML,
            $this->plugin->__invoke($element),
        );
    }

    public function testNameCanBeSetByAttributes(): void
    {
        $element = new CheckboxElement();

        self::assertSame(
            <<<'HTML'
            <input name="bar" type="hidden" value="0">
            <input name="bar" type="checkbox" value="1">
            HTML,
            $this->plugin->__invoke($element, ['name' => 'bar']),
        );
    }

    public function testMissingElementNameIsExceptional(): void
    {
        $element = new CheckboxElement();
        $this->expectException(FormElementsMustBeNamed::class);
        $this->plugin->__invoke($element);
    }

    public function testThatNameTypeAndValueAreIgnoredInGivenAttributes(): void
    {
        $element = new CheckboxElement('foo');

        self::assertSame(
            <<<'HTML'
            <input name="foo" type="hidden" value="0">
            <input name="foo" type="checkbox" value="1">
            HTML,
            $this->plugin->__invoke($element, ['name' => 'bar', 'type' => 'radio', 'value' => 99]),
        );
    }

    public function testInvalidElementAttributesAreModified(): void
    {
        $element = new CheckboxElement('foo');
        $element->setMessages(['Bad News']);

        self::assertSame(
            <<<'HTML'
            <input aria-invalid="true" name="foo" type="hidden" value="0">
            <input aria-invalid="true" name="foo" type="checkbox" value="1">
            HTML,
            $this->plugin->__invoke($element),
        );
    }

    public function testBooleanFalseAttributesAreOmitted(): void
    {
        $element = new CheckboxElement('foo');

        self::assertSame(
            <<<'HTML'
            <input name="foo" type="hidden" value="0">
            <input name="foo" type="checkbox" value="1">
            HTML,
            $this->plugin->__invoke($element, ['disabled' => false]),
        );
    }

    public function testBooleanTrueAttributesAreSimplified(): void
    {
        $element = new CheckboxElement('foo');

        self::assertSame(
            <<<'HTML'
            <input disabled name="foo" type="hidden" value="0">
            <input disabled name="foo" type="checkbox" value="1">
            HTML,
            $this->plugin->__invoke($element, ['disabled' => true]),
        );
    }

    public function testInvalidAttributesAreIgnored(): void
    {
        $element = new CheckboxElement('foo');

        self::assertSame(
            <<<'HTML'
            <input name="foo" type="hidden" value="0">
            <input name="foo" type="checkbox" value="1">
            HTML,
            $this->plugin->__invoke($element, ['goats' => 'monkeys']),
        );
    }

    public function testHiddenElementCanBeDisabled(): void
    {
        $element = new CheckboxElement('foo');
        $element->setUseHiddenElement(false);

        self::assertSame(
            <<<'HTML'
            <input name="foo" type="checkbox" value="1">
            HTML,
            $this->plugin->__invoke($element),
        );
    }

    public function testXhtmlOutput(): void
    {
        $plugin  = new CheckBox(
            new HtmlAttributes(
                new Escaper(),
            ),
            new InvalidElementAttributeHandler(),
            Doctype::XHTML1Strict,
        );
        $element = new CheckboxElement('foo');

        self::assertSame(
            <<<'HTML'
            <input name="foo" type="hidden" value="0" />
            <input name="foo" type="checkbox" value="1" />
            HTML,
            $plugin->__invoke($element),
        );
    }

    public function testElementIdIsOnlyPresentOnTheCheckbox(): void
    {
        $element = new CheckboxElement('foo');
        $element->setAttribute('id', 'baz');

        self::assertSame(
            <<<'HTML'
            <input name="foo" type="hidden" value="0">
            <input id="baz" name="foo" type="checkbox" value="1">
            HTML,
            $this->plugin->__invoke($element),
        );
    }

    public function testThatTheCheckedAttributeWillBeSetWhenTheCurrentValueMatches(): void
    {
        $element = new CheckboxElement('foo');
        $element->setValue(1);

        self::assertSame(
            <<<'HTML'
            <input name="foo" type="hidden" value="0">
            <input checked name="foo" type="checkbox" value="1">
            HTML,
            $this->plugin->__invoke($element),
        );
    }
}
