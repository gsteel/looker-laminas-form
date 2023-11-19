<?php

declare(strict_types=1);

namespace Looker\Form\Test\Plugin;

use Laminas\Form\Element\Text;
use Laminas\Form\Fieldset;
use Looker\Form\Plugin\Fieldset as FieldsetPlugin;
use Looker\Form\Test\PluginManagerSetup;
use Looker\PluginManager;
use PHPUnit\Framework\TestCase;

class FieldsetTest extends TestCase
{
    private FieldsetPlugin $plugin;

    protected function setUp(): void
    {
        /**
         * Because the `FormElement` plugin has a dependency on a plugin manager, it's easier to just build this
         * instance via its factory, otherwise, we have a _lot_ of plugins to set up.
         */
        $container    = PluginManagerSetup::getContainer();
        $plugins      = $container->get(PluginManager::class);
        $this->plugin = $plugins->get(FieldsetPlugin::class);
    }

    public function testThatMarkupIsEmptyForAnEmptyFieldset(): void
    {
        self::assertSame('', $this->plugin->__invoke(new Fieldset()));
    }

    public function testDefaultFieldsetBehaviour(): void
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

    public function testTheFieldsetLabelIsOptional(): void
    {
        $fieldset = new Fieldset('fields');
        $fieldset->setUseAsBaseFieldset(false);
        $element = new Text('foo', ['label' => 'Some Input']);
        $fieldset->add($element);
        $markup = $this->plugin->__invoke($fieldset);
        self::assertSame(
            <<<'HTML'
            <fieldset name="fields">
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

    public function testThatFieldsetMarkupIsOmittedForTheBaseFieldset(): void
    {
        $fieldset = new Fieldset('fields', ['label' => 'Some Fields']);
        $fieldset->setUseAsBaseFieldset(true);
        $element = new Text('foo', ['label' => 'Some Input']);
        $fieldset->add($element);
        $markup = $this->plugin->__invoke($fieldset);
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

    public function testThatFieldsetsCanBeNested(): void
    {
        $fieldset1 = new Fieldset('fields1', ['label' => 'Some Fields']);
        $fieldset1->setUseAsBaseFieldset(false);
        $element = new Text('foo', ['label' => 'Some Input']);
        $fieldset1->add($element);

        $fieldset2 = new Fieldset('fields2', ['label' => 'More Fields']);
        $fieldset2->setUseAsBaseFieldset(false);
        $element = new Text('bar', ['label' => 'More Input']);
        $fieldset2->add($element);

        $fieldset1->add($fieldset2);

        $markup = $this->plugin->__invoke($fieldset1);
        self::assertSame(
            <<<'HTML'
            <fieldset name="fields1">
            <legend>Some Fields</legend>
            <div>
            <label for="foo">
            Some Input
            </label>
            <input name="foo" type="text" value="">
            </div>
            <fieldset name="fields2">
            <legend>More Fields</legend>
            <div>
            <label for="bar">
            More Input
            </label>
            <input name="bar" type="text" value="">
            </div>
            </fieldset>
            </fieldset>
            HTML,
            $markup,
        );
    }
}
