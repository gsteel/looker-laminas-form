<?php

declare(strict_types=1);

namespace Looker\Form\Plugin;

use Laminas\Escaper\Escaper;
use Laminas\Form\Element\Select as SelectElement;
use Looker\Form\HTML\SelectAttribute;
use Looker\Form\Plugin\Exception\FormElementsMustBeNamed;
use Looker\Form\Plugin\Exception\SelectElementCannotBeRendered;
use Looker\HTML\AttributeNormaliser;
use Looker\Plugin\HtmlAttributes;

use function array_merge;
use function array_unshift;
use function assert;
use function implode;
use function is_array;
use function is_scalar;
use function is_string;
use function sprintf;
use function str_contains;

use const PHP_EOL;

final readonly class Select
{
    public function __construct(
        private Escaper $escaper,
        private HtmlAttributes $attributeHelper,
        private Option $optionHelper,
        private InvalidElementAttributeHandler $invalidElementHandler,
    ) {
    }

    /** @param array<string, scalar|null> $attributes */
    public function __invoke(SelectElement $element, array $attributes = []): string
    {
        $attributes = array_merge($element->getAttributes(), $attributes);
        $name       = $attributes['name'] ?? null;
        $name       = $element->getName() ?? $name;
        if (! is_string($name) || $name === '') {
            throw FormElementsMustBeNamed::with($element);
        }

        if ($element->isMultiple() && ! str_contains($name, '[]')) {
            $name .= '[]';
        }

        $attributes['name'] = $name;

        $attributes = ($this->invalidElementHandler)($element, $attributes);

        $attributes = AttributeNormaliser::normalise($attributes, new SelectAttribute());

        return sprintf(
            '<select %1$s>%3$s%2$s%3$s</select>',
            ($this->attributeHelper)($attributes),
            $this->renderOptions($element),
            PHP_EOL,
        );
    }

    private function renderOptions(SelectElement $element): string
    {
        $options       = $element->getValueOptions();
        $optionStrings = [];

        /** @psalm-var mixed $option */
        foreach ($options as $key => $option) {
            if (is_array($option) && isset($option['options']) && is_array($option['options'])) {
                $label           = isset($option['label']) && is_scalar($option['label'])
                    ? (string) $option['label']
                    : '';
                $optionStrings[] = $this->renderOptGroup($element, $label, $option['options']);
                continue;
            }

            $optionStrings[] = $this->renderOption($element, $key, $option);
        }

        if ($element->getEmptyOption() !== null) {
            $empty = $this->renderOption($element, null, $element->getEmptyOption());
            array_unshift($optionStrings, $empty);
        }

        return implode(PHP_EOL, $optionStrings);
    }

    private function renderOption(SelectElement $element, string|int|null $key, mixed $option): string
    {
        if (is_scalar($option)) {
            return ($this->optionHelper)($element, (string) $key, (string) $option, []);
        }

        if (
            is_array($option)
            && isset($option['label'])
            && isset($option['value'])
            && is_scalar($option['label'])
            && is_scalar($option['value'])
        ) {
            $attributes = $option['attributes'] ?? [];
            assert(is_array($attributes));
            unset($option['attributes']);

            $attributes = array_merge($attributes, $option);
            unset($attributes['label'], $attributes['value']);

            return ($this->optionHelper)($element, (string) $option['value'], (string) $option['label'], $attributes);
        }

        throw SelectElementCannotBeRendered::becauseOptionsCannotBeCoerced($element);
    }

    /** @param array<array-key, mixed> $options */
    private function renderOptGroup(SelectElement $element, string $label, array $options): string
    {
        $out = [
            sprintf(
                '<optgroup label="%s">',
                $this->escaper->escapeHtmlAttr($label),
            ),
        ];

        /** @psalm-var mixed $option */
        foreach ($options as $key => $option) {
            $out[] = $this->renderOption($element, $key, $option);
        }

        $out[] = '</optgroup>';

        return implode(PHP_EOL, $out);
    }
}
