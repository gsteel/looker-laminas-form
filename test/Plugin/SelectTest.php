<?php

declare(strict_types=1);

namespace Looker\Form\Test\Plugin;

use Laminas\Escaper\Escaper;
use Laminas\Form\Element\Select as SelectElement;
use Looker\Form\Plugin\Exception\FormElementsMustBeNamed;
use Looker\Form\Plugin\Exception\SelectElementCannotBeRendered;
use Looker\Form\Plugin\InvalidElementAttributeHandler;
use Looker\Form\Plugin\Option;
use Looker\Form\Plugin\Select;
use Looker\Plugin\HtmlAttributes;
use PHPUnit\Framework\TestCase;

class SelectTest extends TestCase
{
    private Select $helper;

    protected function setUp(): void
    {
        $escaper         = new Escaper();
        $attributeHelper = new HtmlAttributes($escaper);
        $this->helper    = new Select(
            $escaper,
            $attributeHelper,
            new Option($escaper, $attributeHelper),
            new InvalidElementAttributeHandler(),
        );
    }

    public function testThatASimpleSelectElementWillBeRendered(): void
    {
        $element = new SelectElement('test', [
            'value_options' => [
                'a' => 'A',
                'b' => 'B',
            ],
        ]);

        $markup = $this->helper->__invoke($element);

        self::assertSame(
            <<<'HTML'
            <select name="test">
            <option value="a">A</option>
            <option value="b">B</option>
            </select>
            HTML,
            $markup,
        );
    }

    public function testThatASimpleSelectElementWithOptionsInSpecFormatWillBeRendered(): void
    {
        $element = new SelectElement('test', [
            'value_options' => [
                [
                    'value' => 'a',
                    'label' => 'A',
                    'attributes' => ['id' => 'aa'],
                ],
                [
                    'value' => 'b',
                    'label' => 'B',
                    'attributes' => ['id' => 'bb'],
                ],
            ],
        ]);

        $markup = $this->helper->__invoke($element);

        self::assertSame(
            <<<'HTML'
            <select name="test">
            <option id="aa" value="a">A</option>
            <option id="bb" value="b">B</option>
            </select>
            HTML,
            $markup,
        );
    }

    public function testThatAnOptionGroupWithScalarOptionsWillBeRendered(): void
    {
        $element = new SelectElement('test', [
            'value_options' => [
                [
                    'label' => 'Stuff',
                    'options' => [
                        'a' => 'A',
                        'b' => 'B',
                    ],
                ],
            ],
        ]);

        $markup = $this->helper->__invoke($element);

        self::assertSame(
            <<<'HTML'
            <select name="test">
            <optgroup label="Stuff">
            <option value="a">A</option>
            <option value="b">B</option>
            </optgroup>
            </select>
            HTML,
            $markup,
        );
    }

    public function testThatAnOptionGroupWithSpecOptionsWillBeRendered(): void
    {
        $element = new SelectElement('test', [
            'value_options' => [
                [
                    'label' => 'Stuff',
                    'options' => [
                        [
                            'value' => 'a',
                            'label' => 'A',
                            'attributes' => ['id' => 'aa'],
                        ],
                        [
                            'value' => 'b',
                            'label' => 'B',
                            'attributes' => ['id' => 'bb'],
                        ],
                    ],
                ],
            ],
        ]);

        $markup = $this->helper->__invoke($element);

        self::assertSame(
            <<<'HTML'
            <select name="test">
            <optgroup label="Stuff">
            <option id="aa" value="a">A</option>
            <option id="bb" value="b">B</option>
            </optgroup>
            </select>
            HTML,
            $markup,
        );
    }

    public function testThatTopLevelOptionAttributesAreCorrectlyRendered(): void
    {
        $element = new SelectElement('test', [
            'value_options' => [
                [
                    'label' => 'Stuff',
                    'options' => [
                        [
                            'value' => 'a',
                            'label' => 'A',
                            'attributes' => ['id' => 'aa'],
                            'disabled' => true,
                        ],
                        [
                            'value' => 'b',
                            'label' => 'B',
                            'attributes' => ['id' => 'bb'],
                            'lang' => 'en',
                        ],
                    ],
                ],
            ],
        ]);

        $markup = $this->helper->__invoke($element);

        self::assertSame(
            <<<'HTML'
            <select name="test">
            <optgroup label="Stuff">
            <option disabled id="aa" value="a">A</option>
            <option id="bb" lang="en" value="b">B</option>
            </optgroup>
            </select>
            HTML,
            $markup,
        );
    }

    public function testThatAnInvalidOptionSpecWillCauseAnException(): void
    {
        $element = new SelectElement('test', [
            'value_options' => [
                [
                    'foo-value' => 'a',
                    'foo-label' => 'A',
                    'attributes' => ['id' => 'aa'],
                ],
            ],
        ]);

        $this->expectException(SelectElementCannotBeRendered::class);
        $this->expectExceptionMessage(
            'Select value options must be key-value pairs, a spec that represents an optgroup, or',
        );

        $this->helper->__invoke($element);
    }

    public function testThatAnElementWithoutANameIsExceptional(): void
    {
        $element = new SelectElement();
        $this->expectException(FormElementsMustBeNamed::class);
        $this->helper->__invoke($element);
    }

    public function testThatAnElementNameWillBeGivenSquareBracketsWhenMultiple(): void
    {
        $element = new SelectElement('test');
        $element->setAttribute('multiple', true);
        self::assertSame(
            <<<'HTML'
            <select multiple name="test&#x5B;&#x5D;">
            
            </select>
            HTML,
            $this->helper->__invoke($element),
        );
    }

    public function testThatAMultiSelectNameWillNotBeGivenSquareBracketsWhenAlreadyPresent(): void
    {
        $element = new SelectElement('test[]');
        $element->setAttribute('multiple', true);
        self::assertSame(
            <<<'HTML'
            <select multiple name="test&#x5B;&#x5D;">
            
            </select>
            HTML,
            $this->helper->__invoke($element),
        );
    }

    public function testBooleanFalseAttributesAreOmitted(): void
    {
        $element = new SelectElement('test');
        $element->setAttribute('multiple', false);
        self::assertSame(
            <<<'HTML'
            <select name="test">
            
            </select>
            HTML,
            $this->helper->__invoke($element),
        );
    }

    public function testBooleanAttributesAreSimplified(): void
    {
        $element = new SelectElement('test');
        $element->setAttribute('disabled', true);
        self::assertSame(
            <<<'HTML'
            <select disabled name="test">
            
            </select>
            HTML,
            $this->helper->__invoke($element),
        );
    }

    public function testInvalidAttributesAreIgnored(): void
    {
        $element = new SelectElement('test');
        $element->setAttribute('boats', 'float');
        self::assertSame(
            <<<'HTML'
            <select name="test">
            
            </select>
            HTML,
            $this->helper->__invoke($element),
        );
    }

    public function testTheAttributeArgumentOverridesTheElementAttributes(): void
    {
        $element = new SelectElement('test');
        $element->setAttribute('autocomplete', 'off');
        self::assertSame(
            <<<'HTML'
            <select autocomplete="goats" name="test">
            
            </select>
            HTML,
            $this->helper->__invoke($element, ['autocomplete' => 'goats']),
        );
    }

    public function testTheEmptyOptionIsPrepended(): void
    {
        $element = new SelectElement('test', [
            'value_options' => ['a' => 'A'],
        ]);
        $element->setEmptyOption('Pick One…');
        self::assertSame(
            <<<'HTML'
            <select name="test">
            <option value="">Pick One…</option>
            <option value="a">A</option>
            </select>
            HTML,
            $this->helper->__invoke($element),
        );
    }

    public function testThatOptionSpecsWithNonScalarLabelsCauseAnException(): void
    {
        $element = new SelectElement('test', [
            'value_options' => [
                [
                    'value' => 'a',
                    'label' => ['why would you do this?'],
                ],
                [
                    'value' => 'b',
                    'label' => 'B',
                ],
            ],
        ]);

        $this->expectException(SelectElementCannotBeRendered::class);
        $this->expectExceptionMessage(
            'Select value options must be key-value pairs, a spec that represents an optgroup, or',
        );
        $this->helper->__invoke($element);
    }

    public function testThatNonScalarOptGroupLabelsWillBeCastToAnEmptyString(): void
    {
        $element = new SelectElement('test', [
            'value_options' => [
                [
                    'label' => ['why would you do this?'],
                    'options' => [
                        'a' => 'A',
                        'b' => 'B',
                    ],
                ],
            ],
        ]);

        self::assertSame(
            <<<'HTML'
            <select name="test">
            <optgroup label="">
            <option value="a">A</option>
            <option value="b">B</option>
            </optgroup>
            </select>
            HTML,
            $this->helper->__invoke($element),
        );
    }
}
