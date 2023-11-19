<?php

declare(strict_types=1);

namespace Looker\Form\Plugin;

use Laminas\Form\Element;
use Laminas\Form\Element\Password;
use Laminas\Form\ElementInterface;
use Looker\Form\HTML\InputAttribute;
use Looker\Form\Plugin\Exception\FormElementsMustBeNamed;
use Looker\Form\Plugin\Exception\InvalidElementType;
use Looker\HTML\AttributeNormaliser;
use Looker\Plugin\HtmlAttributes;
use Looker\Value\Doctype;

use function array_key_exists;
use function array_merge;
use function is_string;
use function sprintf;

final readonly class FormInput
{
    private const TYPE_MAP = [
        Element\Checkbox::class => 'checkbox',
        Element\Color::class => 'color',
        Element\Date::class => 'date',
        Element\DateTimeLocal::class => 'datetime-local',
        Element\Email::class => 'email',
        Element\File::class => 'file',
        Element\Hidden::class => 'hidden',
        Element\Image::class => 'image',
        Element\Month::class => 'month',
        Element\Number::class => 'number',
        Element\Password::class => 'password',
        Element\Radio::class => 'radio',
        Element\Range::class => 'range',
        Element\Search::class => 'search',
        Element\Submit::class => 'submit',
        Element\Tel::class => 'tel',
        Element\Text::class => 'text',
        Element\Time::class => 'time',
        Element\Url::class => 'url',
        Element\Week::class => 'week',
    ];

    public function __construct(
        private Doctype $doctype,
        private HtmlAttributes $attributeHelper,
        private InvalidElementAttributeHandler $invalidElementHandler,
    ) {
    }

    /** @param array<string, scalar|null> $attributes */
    public function __invoke(ElementInterface $element, array $attributes = []): string
    {
        $elementClass = $element::class;
        if (! array_key_exists($elementClass, self::TYPE_MAP)) {
            throw InvalidElementType::becauseOfUnHandledType($element, self::class);
        }

        $name = $attributes['name'] ?? null;
        $name = $element->getName() ?? $name;
        if (! is_string($name) || $name === '') {
            throw FormElementsMustBeNamed::with($element);
        }

        $attributes          = array_merge($element->getAttributes(), $attributes);
        $attributes['name']  = $name;
        $attributes['type']  = self::TYPE_MAP[$elementClass];
        $attributes['value'] = (string) $element->getValue();
        if ($element instanceof Password) {
            unset($attributes['value']);
        }

        $attributes = ($this->invalidElementHandler)($element, $attributes);

        $attributes = AttributeNormaliser::normalise($attributes, new InputAttribute());

        return sprintf(
            '<input %s%s',
            $this->attributeHelper->__invoke($attributes),
            $this->doctype->isXhtml() ? ' />' : '>',
        );
    }
}
