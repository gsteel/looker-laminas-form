<?php

declare(strict_types=1);

namespace Looker\Form\Test\Plugin;

use Laminas\Form\Element\Text;
use Laminas\Form\Form as LaminasForm;
use Looker\Form\Plugin\Form;
use Looker\Form\Test\PluginManagerSetup;
use Looker\PluginManager;
use PHPUnit\Framework\TestCase;

class FormTest extends TestCase
{
    private Form $plugin;

    protected function setUp(): void
    {
        /**
         * Because the `FormElementRow` plugin has a dependency on a plugin manager, it's easier to just build this
         * instance via its factory, otherwise, we have a _lot_ of plugins to set up.
         */
        $container    = PluginManagerSetup::getContainer();
        $plugins      = $container->get(PluginManager::class);
        $this->plugin = $plugins->get(Form::class);
    }

    public function testOpenTagForFormWithBasicAttributes(): void
    {
        $form = new LaminasForm();
        $form->setName('form-name');
        $form->setAttributes([
            'method' => 'post',
            'action' => '/somewhere',
        ]);

        self::assertSame(
            '<form action="&#x2F;somewhere" method="post" name="form-name">',
            $this->plugin->openTag($form),
        );
    }

    public function testThatAttributesGivenOverrideThoseInTheForm(): void
    {
        $form = new LaminasForm();
        $form->setName('form-name');
        $form->setAttributes([
            'method' => 'post',
            'action' => '/somewhere',
        ]);

        self::assertSame(
            '<form action="foo" method="GET" name="form-name">',
            $this->plugin->openTag($form, ['action' => 'foo', 'METHOD' => 'GET']),
        );
    }

    public function testUnknownAttributesAreIgnored(): void
    {
        self::assertSame(
            '<form method="POST">',
            $this->plugin->openTag(new LaminasForm(), ['foo' => 'bar']),
        );
    }

    public function testBooleanFalseAttributesAreIgnored(): void
    {
        self::assertSame(
            '<form method="POST">',
            $this->plugin->openTag(new LaminasForm(), ['novalidate' => false]),
        );
    }

    public function testBooleanTrueAttributesAreMinimised(): void
    {
        self::assertSame(
            '<form method="POST" novalidate>',
            $this->plugin->openTag(new LaminasForm(), ['novalidate' => true]),
        );
    }

    public function testInvokeReturnsSelfWhenFormIsNull(): void
    {
        self::assertSame(
            $this->plugin,
            $this->plugin->__invoke(),
        );
    }

    public function testRenderOfSingleFieldForm(): void
    {
        $form = new LaminasForm();
        $form->add(new Text('fred', ['label' => 'Fred']));

        self::assertSame(
            <<<'HTML'
            <form method="POST">
            <div>
            <label for="fred">
            Fred
            </label>
            <input name="fred" type="text" value="">
            </div>
            </form>
            HTML,
            $this->plugin->__invoke($form),
        );
    }

    public function testThatTheFormIsPrepared(): void
    {
        $form = new LaminasForm('stuff');
        $form->setWrapElements(true);
        $form->add(new Text('fred', ['label' => 'Fred']));

        self::assertSame(
            <<<'HTML'
            <form method="POST" name="stuff">
            <div>
            <label for="stuff&#x5B;fred&#x5D;">
            Fred
            </label>
            <input name="stuff&#x5B;fred&#x5D;" type="text" value="">
            </div>
            </form>
            HTML,
            $this->plugin->__invoke($form),
        );
    }
}
