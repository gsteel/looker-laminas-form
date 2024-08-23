<?php

declare(strict_types=1);

namespace Looker\Form\Test\Plugin;

use Laminas\Escaper\Escaper;
use Laminas\Form\Element\Text;
use Looker\Form\Plugin\Exception\FormElementsMustBeNamed;
use Looker\Form\Plugin\Label;
use Looker\Plugin\HtmlAttributes;
use PHPUnit\Framework\TestCase;

class LabelTest extends TestCase
{
    private Label $helper;

    protected function setUp(): void
    {
        $escaper      = new Escaper();
        $this->helper = new Label(
            $escaper,
            new HtmlAttributes($escaper),
        );
    }

    public function testInvokeReturnsHelperWithNoArguments(): void
    {
        self::assertSame($this->helper, $this->helper->__invoke());
    }

    public function testThatAnElementWithANameAndALabelWillRenderALabel(): void
    {
        self::assertSame(
            '<label for="foo">Baz</label>',
            ($this->helper)(new Text('foo', ['label' => 'Baz'])),
        );
    }

    public function testThatElementLabelIsEscaped(): void
    {
        self::assertSame(
            '<label for="foo">1 &amp; 2</label>',
            ($this->helper)(new Text('foo', ['label' => '1 & 2'])),
        );
    }

    public function testThatTheElementIdIsPreferredOverTheName(): void
    {
        $text = new Text('foo', ['label' => 'Foo']);
        $text->setAttribute('id', 'bar');
        self::assertSame(
            '<label for="bar">Foo</label>',
            ($this->helper)($text),
        );
    }

    public function testThatTheElementLabelAttributesAreConsidered(): void
    {
        $text = new Text('foo', ['label' => 'Bar']);
        $text->setLabelAttributes(['class' => 'bing']);
        self::assertSame(
            '<label class="bing" for="foo">Bar</label>',
            ($this->helper)($text),
        );
    }

    public function testThatAttributesAreOverriddenByArguments(): void
    {
        $text = new Text('foo', ['label' => 'Blip']);
        $text->setLabelAttributes(['class' => 'bing']);
        self::assertSame(
            '<label class="pink" for="foo">Blip</label>',
            ($this->helper)($text, ['class' => 'pink']),
        );
    }

    public function testThatAnElementWithNoLabelWillYieldAnEmptyString(): void
    {
        self::assertSame('', $this->helper->__invoke(new Text()));
    }

    public function testAnExceptionIsThrownWhenAnElementNameOrIdCannotBeDetermined(): void
    {
        $this->expectException(FormElementsMustBeNamed::class);
        $this->helper->__invoke(new Text(null, ['label' => 'Baz']));
    }

    public function testThatEscapeCanBeDisabledWithLegacyLaminasOption(): void
    {
        $text = new Text('foo', [
            'label' => '<span>Blip</span>',
            'label_options' => ['disable_html_escape' => true],
        ]);

        self::assertSame(
            '<label for="foo"><span>Blip</span></label>',
            $this->helper->__invoke($text),
        );
    }

    public function testThatEscapeCanBeDisabledWithEscapeOption(): void
    {
        $text = new Text('foo', [
            'label' => '<span>Blip</span>',
            'label_options' => ['escape' => false],
        ]);

        self::assertSame(
            '<label for="foo"><span>Blip</span></label>',
            $this->helper->__invoke($text),
        );
    }

    public function testThatEscapeOptionOverridesLaminasOption(): void
    {
        $text = new Text('foo', [
            'label' => '&',
            'label_options' => [
                'disable_html_escape' => true,
                'escape' => true,
            ],
        ]);

        self::assertSame(
            '<label for="foo">&amp;</label>',
            $this->helper->__invoke($text),
        );
    }
}
