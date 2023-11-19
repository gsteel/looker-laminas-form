<?php

declare(strict_types=1);

namespace Looker\Form\Test\Plugin;

use Laminas\Form\Element\Text;
use Laminas\Form\Fieldset;
use Looker\Form\Plugin\FormElementRow;
use Looker\Form\Test\PluginManagerSetup;
use Looker\PluginManager;
use PHPUnit\Framework\TestCase;

class FormElementRowTest extends TestCase
{
    private FormElementRow $plugin;

    protected function setUp(): void
    {
        /**
         * Because the `FormElement` plugin has a dependency on a plugin manager, it's easier to just build this
         * instance via its factory, otherwise, we have a _lot_ of plugins to set up.
         */
        $container    = PluginManagerSetup::getContainer();
        $plugins      = $container->get(PluginManager::class);
        $this->plugin = $plugins->get(FormElementRow::class);
    }

    public function testDefaultBehaviour(): void
    {
        $element = new Text('foo', ['label' => 'Some Input']);
        $markup  = $this->plugin->__invoke($element);
        self::assertSame(
            <<<'HTML'
            <div>
            <label for="foo">
            Some Input
            </label>
            <input name="foo" type="text" value="">
            </div>
            HTML,
            $markup,
        );
    }

    public function testThatWrapperAttributesAreApplied(): void
    {
        $element = new Text('foo', ['label' => 'Some Input']);
        $markup  = $this->plugin->__invoke($element, [], [], [], ['class' => 'foo']);
        self::assertSame(
            <<<'HTML'
            <div class="foo">
            <label for="foo">
            Some Input
            </label>
            <input name="foo" type="text" value="">
            </div>
            HTML,
            $markup,
        );
    }

    public function testThatElementAttributesAreApplied(): void
    {
        $element = new Text('foo', ['label' => 'Some Input']);
        $markup  = $this->plugin->__invoke($element, ['class' => 'input']);
        self::assertSame(
            <<<'HTML'
            <div>
            <label for="foo">
            Some Input
            </label>
            <input class="input" name="foo" type="text" value="">
            </div>
            HTML,
            $markup,
        );
    }

    public function testThatLabelAttributesAreApplied(): void
    {
        $element = new Text('foo', ['label' => 'Some Input']);
        $markup  = $this->plugin->__invoke($element, [], ['class' => 'label']);
        self::assertSame(
            <<<'HTML'
            <div>
            <label class="label" for="foo">
            Some Input
            </label>
            <input name="foo" type="text" value="">
            </div>
            HTML,
            $markup,
        );
    }

    public function testLabelAppendDoesNotAppendWithoutWrap(): void
    {
        $element = new Text('foo', ['label' => 'Some Input']);
        $markup  = $this->plugin->__invoke($element, [], [], [], [], FormElementRow::APPEND);
        self::assertSame(
            <<<'HTML'
            <div>
            <label for="foo">
            Some Input
            </label>
            <input name="foo" type="text" value="">
            </div>
            HTML,
            $markup,
        );
    }

    public function testLabelIsAppendedWhenWrapped(): void
    {
        $element = new Text('foo', ['label' => 'Some Input']);
        $markup  = $this->plugin->__invoke(
            $element,
            [],
            [],
            [],
            [],
            FormElementRow::APPEND,
            FormElementRow::APPEND,
            true,
        );
        self::assertSame(
            <<<'HTML'
            <div>
            <label for="foo">
            <input name="foo" type="text" value="">
            Some Input
            </label>
            </div>
            HTML,
            $markup,
        );
    }

    public function testLabelIsPrependedWhenWrapped(): void
    {
        $element = new Text('foo', ['label' => 'Some Input']);
        $markup  = $this->plugin->__invoke(
            $element,
            [],
            [],
            [],
            [],
            FormElementRow::PREPEND,
            FormElementRow::APPEND,
            true,
        );
        self::assertSame(
            <<<'HTML'
            <div>
            <label for="foo">
            Some Input
            <input name="foo" type="text" value="">
            </label>
            </div>
            HTML,
            $markup,
        );
    }

    public function testErrorsAreAppendedByDefault(): void
    {
        $element = new Text('foo', ['label' => 'Some Input']);
        $element->setMessages(['Bad News']);
        $markup = $this->plugin->__invoke($element);
        self::assertSame(
            <<<'HTML'
            <div>
            <label for="foo">
            Some Input
            </label>
            <input aria-invalid="true" name="foo" type="text" value="">
            <ul class="error-list">
            <li>Bad News</li>
            </ul>
            </div>
            HTML,
            $markup,
        );
    }

    public function testErrorsCanBePrepended(): void
    {
        $element = new Text('foo', ['label' => 'Some Input']);
        $element->setMessages(['Bad News']);
        $markup = $this->plugin->__invoke($element, errorPosition: 'prepend');
        self::assertSame(
            <<<'HTML'
            <div>
            <label for="foo">
            Some Input
            </label>
            <ul class="error-list">
            <li>Bad News</li>
            </ul>
            <input aria-invalid="true" name="foo" type="text" value="">
            </div>
            HTML,
            $markup,
        );
    }

    public function testThatFieldsetsAreNotTreatedLikeAnElement(): void
    {
        $fieldset = new Fieldset('fields', ['label' => 'Some Fields']);
        $fieldset->setUseAsBaseFieldset(false);
        $element = new Text('foo', ['label' => 'Some Input']);
        $fieldset->add($element);
        $markup = $this->plugin->__invoke($fieldset);
        self::assertSame(
            <<<'HTML'
            <fieldset name="fields">
            <legend>Some Fields</legend>
            <div>
            <label for="foo">
            Some Input
            </label>
            <input name="foo" type="text" value="">
            </div>
            </fieldset>
            HTML,
            $markup,
        );
    }
}
