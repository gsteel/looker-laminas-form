<?php

declare(strict_types=1);

namespace Looker\Form\Plugin;

use Laminas\Form\Element\Checkbox as CheckboxElement;
use Looker\Form\HTML\InputAttribute;
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
        private AttributeNormaliser $attributeNormaliser,
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
        unset($attributes['name']);
        if (is_string($name) && $name !== '') {
            $attributes['name'] = $name;
        }

        $attributes = array_merge($element->getAttributes(), $attributes);
        $attributes['type'] = 'checkbox';
        $attributes['value'] = $element->getCheckedValue();
        $closingBracket = $this->doctype->isXhtml() ? ' />' : '>';
        $attributes = ($this->invalidElementHandler)($element, $attributes);

        if ((string) $element->getValue() === $element->getCheckedValue()) {
            $attributes['checked'] = true;
        }

        $attributes = $this->attributeNormaliser->normalise($attributes, new InputAttribute());

        $elementMarkup = sprintf('<input %s%s', ($this->attributePlugin)($attributes), $closingBracket);

        if (! $element->useHiddenElement()) {
            return $elementMarkup;
        }

        unset($attributes['id'], $attributes['checked']);
        $attributes['value'] = $element->getUncheckedValue();
        $attributes['type'] = 'hidden';
        $hiddenMarkup = sprintf('<input %s%s', ($this->attributePlugin)($attributes), $closingBracket);

        return $hiddenMarkup . PHP_EOL . $elementMarkup;
    }
}
