<?php

declare(strict_types=1);

namespace Looker\Form\Test\Plugin;

use Laminas\Escaper\Escaper;
use Laminas\Form\Element\Text;
use Looker\Form\Plugin\ElementErrorList;
use Looker\Plugin\HtmlAttributes;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\TestCase;

class ElementErrorListTest extends TestCase
{
    private ElementErrorList $plugin;
    private Text $element;

    protected function setUp(): void
    {
        $this->element = new Text();
        $escaper       = new Escaper();
        $this->plugin  = new ElementErrorList(
            $escaper,
            new HtmlAttributes($escaper),
        );
    }

    public function testThatOutputIsEmptyForElementsWithoutMessages(): void
    {
        self::assertSame('', $this->plugin->__invoke($this->element));
    }

    public function testThatMessagesWillBeEscaped(): void
    {
        $this->element->setMessages(['foo' => '1 & 2']);

        self::assertSame(
            <<<'HTML'
            <ul>
            <li>1 &amp; 2</li>
            </ul>
            HTML,
            $this->plugin->__invoke($this->element),
        );
    }

    public function testThatAttributesWillBeAppliedToTheList(): void
    {
        $this->element->setMessages(['foo' => '1 & 2']);

        self::assertSame(
            <<<'HTML'
            <ul class="baz">
            <li>1 &amp; 2</li>
            </ul>
            HTML,
            $this->plugin->__invoke($this->element, ['class' => 'baz']),
        );
    }

    public function testThatNestedMessagesWillBeFlattened(): void
    {
        $this->element->setMessages([
            'a',
            [
                'b',
                [
                    'c',
                    ['d'],
                ],
            ],
        ]);

        self::assertSame(
            <<<'HTML'
            <ul>
            <li>a</li>
            <li>b</li>
            <li>c</li>
            <li>d</li>
            </ul>
            HTML,
            $this->plugin->__invoke($this->element),
        );
    }

    public function testThatDefaultAttributesWillBeApplied(): ElementErrorList
    {
        $escaper = new Escaper();
        $plugin  = new ElementErrorList(
            $escaper,
            new HtmlAttributes($escaper),
            ['class' => 'errors'],
        );
        $this->element->setMessages(['foo']);

        self::assertSame(
            <<<'HTML'
            <ul class="errors">
            <li>foo</li>
            </ul>
            HTML,
            $plugin->__invoke($this->element),
        );

        return $plugin;
    }

    #[Depends('testThatDefaultAttributesWillBeApplied')]
    public function testThatAttributeArgumentOverridesDefaultAttributes(ElementErrorList $plugin): void
    {
        $this->element->setMessages(['foo']);

        self::assertSame(
            <<<'HTML'
            <ul class="goats">
            <li>foo</li>
            </ul>
            HTML,
            $plugin->__invoke($this->element, ['class' => 'goats']),
        );
    }
}
