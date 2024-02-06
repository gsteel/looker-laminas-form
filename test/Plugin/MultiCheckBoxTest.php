<?php

declare(strict_types=1);

namespace Looker\Form\Test\Plugin;

use Laminas\Escaper\Escaper;
use Laminas\Form\Element\MultiCheckbox as MultiCheckElement;
use Laminas\Form\Element\Radio;
use Looker\Form\Plugin\Exception\MultiCheckBoxCannotBeRendered;
use Looker\Form\Plugin\MultiCheckBox;
use Looker\Plugin\HtmlAttributes;
use Looker\Value\Doctype;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MultiCheckBoxTest extends TestCase
{
    private MultiCheckBox $plugin;

    protected function setUp(): void
    {
        $escaper    = new Escaper();
        $attributes = new HtmlAttributes($escaper);

        $this->plugin = new MultiCheckBox(
            $escaper,
            $attributes,
            Doctype::HTML5,
        );
    }

    public function testThatOutputWillBeEmptyWithNoValueOptions(): void
    {
        $element = new MultiCheckElement('foo');
        self::assertSame('', $this->plugin->__invoke($element));
    }

    public function testBasicRenderOfMultiCheckBox(): void
    {
        $element = new MultiCheckElement('foo');
        $element->setValueOptions([
            'a' => 'Foo',
            'b' => 'Bar',
        ]);

        self::assertSame(
            <<<'HTML'
            <label><input name="foo&#x5B;&#x5D;" type="checkbox" value="a"> Foo</label>
            <label><input name="foo&#x5B;&#x5D;" type="checkbox" value="b"> Bar</label>
            HTML,
            $this->plugin->__invoke($element),
        );
    }

    public function testBasicRenderOfRadio(): void
    {
        $element = new Radio('foo');
        $element->setValueOptions([
            'a' => 'Foo',
            'b' => 'Bar',
        ]);

        self::assertSame(
            <<<'HTML'
            <label><input name="foo" type="radio" value="a"> Foo</label>
            <label><input name="foo" type="radio" value="b"> Bar</label>
            HTML,
            $this->plugin->__invoke($element),
        );
    }

    public function testLabelPosition(): void
    {
        $element = new MultiCheckElement('foo');
        $element->setValueOptions([
            'a' => 'Foo',
            'b' => 'Bar',
        ]);

        self::assertSame(
            <<<'HTML'
            <label>Foo <input name="foo&#x5B;&#x5D;" type="checkbox" value="a"></label>
            <label>Bar <input name="foo&#x5B;&#x5D;" type="checkbox" value="b"></label>
            HTML,
            $this->plugin->__invoke($element, MultiCheckBox::PREPEND),
        );
    }

    public function testThatTheHiddenElementWillBeAddedWhenEnabled(): void
    {
        $element = new MultiCheckElement('foo');
        $element->setUseHiddenElement(true);
        $element->setValueOptions([
            'a' => 'Foo',
            'b' => 'Bar',
        ]);

        self::assertSame(
            <<<'HTML'
            <input name="foo" type="hidden" value="">
            <label><input name="foo&#x5B;&#x5D;" type="checkbox" value="a"> Foo</label>
            <label><input name="foo&#x5B;&#x5D;" type="checkbox" value="b"> Bar</label>
            HTML,
            $this->plugin->__invoke($element),
        );
    }

    public function testThatTheHiddenElementWillBeAddedWhenThereAreNoValueOptions(): void
    {
        $element = new MultiCheckElement('foo');
        $element->setUseHiddenElement(true);
        $element->setUncheckedValue('nope');

        self::assertSame(
            <<<'HTML'
            <input name="foo" type="hidden" value="nope">
            HTML,
            $this->plugin->__invoke($element),
        );
    }

    public function testThatTheElementValueWillBeUsedToDetermineCheckedStatus(): void
    {
        $element = new MultiCheckElement('foo');
        $element->setValueOptions([
            'a' => 'Foo',
            'b' => 'Bar',
            'c' => 'Baz',
        ]);
        $element->setValue(['a', 'c']);

        self::assertSame(
            <<<'HTML'
            <label><input checked name="foo&#x5B;&#x5D;" type="checkbox" value="a"> Foo</label>
            <label><input name="foo&#x5B;&#x5D;" type="checkbox" value="b"> Bar</label>
            <label><input checked name="foo&#x5B;&#x5D;" type="checkbox" value="c"> Baz</label>
            HTML,
            $this->plugin->__invoke($element),
        );
    }

    public function testThatCheckedStatusCanBeSetWithValueOptions(): void
    {
        $element = new MultiCheckElement('foo');
        $element->setValueOptions([
            ['value' => 'a', 'label' => 'Foo', 'selected' => true],
            ['value' => 'b', 'label' => 'Bar', 'selected' => false],
            ['value' => 'c', 'label' => 'Baz', 'selected' => false],
        ]);

        self::assertSame(
            <<<'HTML'
            <label><input checked name="foo&#x5B;&#x5D;" type="checkbox" value="a"> Foo</label>
            <label><input name="foo&#x5B;&#x5D;" type="checkbox" value="b"> Bar</label>
            <label><input name="foo&#x5B;&#x5D;" type="checkbox" value="c"> Baz</label>
            HTML,
            $this->plugin->__invoke($element),
        );
    }

    public function testThatCheckedStatusIsOverriddenWhenValueIsPresent(): void
    {
        $element = new MultiCheckElement('foo');
        $element->setValueOptions([
            ['value' => 'a', 'label' => 'Foo', 'selected' => true],
            ['value' => 'b', 'label' => 'Bar', 'selected' => false],
            ['value' => 'c', 'label' => 'Baz', 'selected' => false],
        ]);
        $element->setValue(['c']);

        self::assertSame(
            <<<'HTML'
            <label><input name="foo&#x5B;&#x5D;" type="checkbox" value="a"> Foo</label>
            <label><input name="foo&#x5B;&#x5D;" type="checkbox" value="b"> Bar</label>
            <label><input checked name="foo&#x5B;&#x5D;" type="checkbox" value="c"> Baz</label>
            HTML,
            $this->plugin->__invoke($element),
        );
    }

    /** @return list<array{0: mixed}> */
    public static function empties(): array
    {
        return [
            [''],
            [[]],
            [null],
        ];
    }

    #[DataProvider('empties')]
    public function testAnEmptyValueLeavesAllUnchecked(mixed $value): void
    {
        $element = new MultiCheckElement('foo');
        $element->setValueOptions([
            'a' => 'Foo',
            'b' => 'Bar',
        ]);
        $element->setValue($value);

        self::assertSame(
            <<<'HTML'
            <label><input name="foo&#x5B;&#x5D;" type="checkbox" value="a"> Foo</label>
            <label><input name="foo&#x5B;&#x5D;" type="checkbox" value="b"> Bar</label>
            HTML,
            $this->plugin->__invoke($element),
        );
    }

    public function testValidStringValueChecksTheCorrectBox(): void
    {
        $element = new MultiCheckElement('foo');
        $element->setValueOptions([
            'a' => 'Foo',
            'b' => 'Bar',
        ]);
        $element->setValue('b');

        self::assertSame(
            <<<'HTML'
            <label><input name="foo&#x5B;&#x5D;" type="checkbox" value="a"> Foo</label>
            <label><input checked name="foo&#x5B;&#x5D;" type="checkbox" value="b"> Bar</label>
            HTML,
            $this->plugin->__invoke($element),
        );
    }

    public function testNonScalarAndNonArrayValueIsExceptional(): void
    {
        $element = new MultiCheckElement('foo');
        $element->setValueOptions([
            'a' => 'Foo',
            'b' => 'Bar',
        ]);
        $element->setValue((object) ['foo' => 1]);
        $this->expectException(MultiCheckBoxCannotBeRendered::class);
        $this->expectExceptionMessage('is a multi-checkbox, but its selected value is not iterable');
        $this->plugin->__invoke($element);
    }

    public function testWeirdValueOptionsCauseExceptions(): void
    {
        $element = new MultiCheckElement('foo');
        /** @psalm-suppress InvalidArgument */
        $element->setValueOptions([
            (object) ['a' => 'b'],
        ]);
        $this->expectException(MultiCheckBoxCannotBeRendered::class);
        $this->expectExceptionMessage('Value options should be simple scalar key-value pairs, or a specification');
        $this->plugin->__invoke($element);
    }

    public function testCheckboxAttributesAreApplied(): void
    {
        $element = new MultiCheckElement('foo');
        $element->setValueOptions([
            [
                'label' => 'Foo',
                'value' => 'a',
                'attributes' => [
                    'id' => 'bing',
                    'class' => 'boo',
                ],
            ],
        ]);

        self::assertSame(
            <<<'HTML'
            <label><input class="boo" id="bing" name="foo&#x5B;&#x5D;" type="checkbox" value="a"> Foo</label>
            HTML,
            $this->plugin->__invoke($element),
        );
    }

    public function testLabelAttributesAreApplied(): void
    {
        $element = new MultiCheckElement('foo');
        $element->setValueOptions([
            [
                'label' => 'Foo',
                'value' => 'a',
                'label_attributes' => [
                    'id' => 'bing',
                    'class' => 'boo',
                ],
            ],
        ]);

        self::assertSame(
            <<<'HTML'
            <label class="boo" id="bing"><input name="foo&#x5B;&#x5D;" type="checkbox" value="a"> Foo</label>
            HTML,
            $this->plugin->__invoke($element),
        );
    }

    public function testExceptionThrownForInvalidOptionSpec(): void
    {
        $element = new MultiCheckElement('foo');
        /** @psalm-suppress InvalidArgument */
        $element->setValueOptions([
            [
                'label' => (object) ['a' => 'b'],
                'value' => 'a',
            ],
        ]);
        $this->expectException(MultiCheckBoxCannotBeRendered::class);
        $this->expectExceptionMessage('Value options should be simple scalar key-value pairs, or a specification');
        $this->plugin->__invoke($element);
    }
}
