<?php

declare(strict_types=1);

namespace Looker\Form\Plugin;

use Laminas\Form\Element\Checkbox as CheckboxElement;
use Looker\Form\HTML\InputAttribute;
use Looker\Form\Plugin\Exception\FormElementsMustBeNamed;
use Looker\HTML\AttributeNormaliser;
use Looker\Plugin\HtmlAttributes;
use Looker\Value\Doctype;

use function array_merge;
use function is_string;
use function sprintf;

use const PHP_EOL;

final readonly class CheckBox
{
    public function __construct(
        private HtmlAttributes $attributePlugin,
        private InvalidElementAttributeHandler $invalidElementHandler,
        private Doctype $doctype,
    ) {
    }

    /** @param array<string, scalar|null> $attributes */
    public function __invoke(
        CheckboxElement $element,
        array $attributes = [],
    ): string {
        $name = $attributes['name'] ?? null;
        $name = $element->getName() ?? $name;
        if (! is_string($name) || $name === '') {
            throw FormElementsMustBeNamed::with($element);
        }

        $attributes          = array_merge($element->getAttributes(), $attributes);
        $attributes['name']  = $name;
        $attributes['type']  = 'checkbox';
        $attributes['value'] = $element->getCheckedValue();
        $closingBracket      = $this->doctype->isXhtml() ? ' />' : '>';
        $attributes          = ($this->invalidElementHandler)($element, $attributes);
        $attributes          = AttributeNormaliser::normalise($attributes, new InputAttribute());

        $elementMarkup = sprintf('<input %s%s', ($this->attributePlugin)($attributes), $closingBracket);

        if (! $element->useHiddenElement()) {
            return $elementMarkup;
        }

        unset($attributes['id']);
        $attributes['value'] = $element->getUncheckedValue();
        $attributes['type']  = 'hidden';
        $hiddenMarkup        = sprintf('<input %s%s', ($this->attributePlugin)($attributes), $closingBracket);

        return $hiddenMarkup . PHP_EOL . $elementMarkup;
    }
}
