<?php

declare(strict_types=1);

namespace Looker\Form\Plugin;

use Laminas\Form;
use Laminas\Form\ElementInterface;
use Laminas\Form\Form as LaminasForm;
use Looker\Form\Plugin\Form as FormPlugin;
use Psr\Container\ContainerInterface;

final readonly class FormElement
{
    public function __construct(private ContainerInterface $plugins)
    {
    }

    /** @param array<string, scalar|null> $attributes */
    public function __invoke(ElementInterface $element, array $attributes = []): string
    {
        return match ($element::class) {
            Form\Element\Button::class => ($this->plugins->get(Button::class))($element, null, true, $attributes),
            Form\Element\Checkbox::class => ($this->plugins->get(CheckBox::class))($element, $attributes),
            Form\Element\Color::class,
            Form\Element\Date::class,
            Form\Element\DateTimeLocal::class,
            Form\Element\Email::class,
            Form\Element\File::class,
            Form\Element\Hidden::class,
            Form\Element\Image::class,
            Form\Element\Month::class,
            Form\Element\Number::class,
            Form\Element\Password::class,
            Form\Element\Range::class,
            Form\Element\Search::class,
            Form\Element\Submit::class,
            Form\Element\Tel::class,
            Form\Element\Text::class,
            Form\Element\Time::class,
            Form\Element\Url::class,
            Form\Element\Week::class => ($this->plugins->get(FormInput::class))($element, $attributes),
            Form\Element\Select::class => ($this->plugins->get(Select::class))($element, $attributes),
            Form\Element\Textarea::class => ($this->plugins->get(Textarea::class))($element, $attributes),
            LaminasForm::class => ($this->plugins->get(FormPlugin::class))($element, $attributes),
            Form\Fieldset::class => ($this->plugins->get(Fieldset::class))($element, $attributes),
            Form\Element\MultiCheckbox::class,
            Form\Element\Radio::class => ($this->plugins->get(MultiCheckBox::class))($element),
        };
    }
}
