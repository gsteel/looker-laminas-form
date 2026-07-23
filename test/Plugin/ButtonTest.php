<?php

declare(strict_types=1);

namespace Looker\Form\Test\Plugin;

use Laminas\Escaper\Escaper;
use Laminas\Form\Element\Button as ButtonElement;
use Laminas\Form\Element\Submit;
use Looker\Form\Plugin\Button;
use Looker\Form\Plugin\Exception\ButtonsNeedADescriptiveLabel;
use Looker\Form\Test\Asset\StringableObject;
use Looker\HTML\AttributeNormaliser;
use Looker\Plugin\HtmlAttributes;
use Override;
use PHPUnit\Framework\TestCase;

final class ButtonTest extends TestCase
{
    private Button $plugin;

    #[Override]
    protected function setUp(): void
    {
        $escaper      = new Escaper();
        $this->plugin = new Button(
            $escaper,
            new HtmlAttributes($escaper),
            new AttributeNormaliser(true),
        );
    }

    public function testASubmitButtonWithALabel(): void
    {
        $submit = new Submit();
        $submit->setLabel('Some Button');

        self::assertSame(
            '<button type="submit">Some Button</button>',
            $this->plugin->__invoke($submit),
        );
    }

    public function testASubmitButtonWithANameLabelAndValue(): void
    {
        $submit = new Submit('foo');
        $submit->setLabel('Some Button');
        $submit->setValue('goats');

        self::assertSame(
            '<button name="foo" type="submit" value="goats">Some Button</button>',
            $this->plugin->__invoke($submit),
        );
    }

    public function testSubmitWithNoLabelAndNoContentIsExceptional(): void
    {
        $this->expectException(ButtonsNeedADescriptiveLabel::class);
        $this->expectExceptionMessage(
            'Buttons should have either a label, or you should pass some content into the plugin. The element named '
            . '"fred" does not have a label and no content was provided',
        );

        ($this->plugin)(new Submit('fred'));
    }

    public function testMissingLabelExceptionWithUnNamedElement(): void
    {
        $this->expectException(ButtonsNeedADescriptiveLabel::class);
        $this->expectExceptionMessage(
            'Buttons should have either a label, or you should pass some content into the plugin. The element named '
            . '"un-named" does not have a label and no content was provided',
        );

        ($this->plugin)(new Submit());
    }

    public function testThatContentCanBeProvided(): void
    {
        self::assertSame(
            '<button type="button">Foo &amp; Bar</button>',
            ($this->plugin)(new ButtonElement(), 'Foo & Bar'),
        );
    }

    public function testThatContentCanBeProvidedWithoutEscaping(): void
    {
        self::assertSame(
            '<button type="button"><i class="fa fa-cogs"></i></button>',
            ($this->plugin)(new ButtonElement(), '<i class="fa fa-cogs"></i>', false),
        );
    }

    public function testButtonElementWithLabel(): void
    {
        $button = new ButtonElement('fred');
        $button->setLabel('Foo');

        self::assertSame(
            '<button name="fred" type="button">Foo</button>',
            ($this->plugin)($button),
        );
    }

    public function testAttributesCanBeOverridden(): void
    {
        $button = new ButtonElement();
        $button->setLabel('Foo');
        $button->setAttribute('class', 'btn');

        self::assertSame(
            '<button class="bar" type="button">Foo</button>',
            ($this->plugin)($button, null, true, ['class' => 'bar']),
        );
    }

    public function testBooleanTrueAttributesAreSimplified(): void
    {
        $button = new ButtonElement();

        self::assertSame(
            '<button disabled type="button">Foo</button>',
            ($this->plugin)($button, 'Foo', true, ['disabled' => true]),
        );
    }

    public function testBooleanFalseAttributesAreOmitted(): void
    {
        $button = new ButtonElement();

        self::assertSame(
            '<button type="button">Foo</button>',
            ($this->plugin)($button, 'Foo', true, ['disabled' => false]),
        );
    }

    public function testTheElementValueIsCastToString(): void
    {
        $button = new ButtonElement();
        $button->setValue(new StringableObject('fred'));

        $markup = $this->plugin->__invoke($button, 'Foo');

        self::assertStringContainsString('value="fred"', $markup);
    }
}
