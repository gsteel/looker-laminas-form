<?php

declare(strict_types=1);

namespace Looker\Form\Plugin;

use Laminas\Escaper\Escaper;
use Laminas\Form\Element\MultiCheckbox as MultiCheckboxElement;
use Laminas\Form\Element\Radio;
use Looker\Form\HTML\InputAttribute;
use Looker\Form\HTML\LabelAttribute;
use Looker\Form\Plugin\Exception\FormElementsMustBeNamed;
use Looker\Form\Plugin\Exception\MultiCheckBoxCannotBeRendered;
use Looker\HTML\AttributeNormaliser;
use Looker\Plugin\HtmlAttributes;
use Looker\Value\Doctype;
use Throwable;
use Webmozart\Assert\Assert;

use function array_filter;
use function array_keys;
use function array_map;
use function array_merge;
use function array_unshift;
use function array_values;
use function assert;
use function implode;
use function in_array;
use function is_array;
use function is_scalar;
use function is_string;
use function sprintf;
use function str_ends_with;

use const PHP_EOL;

/**
 * @psalm-type OptionSpec = array{
 *     value: string,
 *     label: string,
 *     attributes: array<string, scalar|null>,
 *     label_attributes: array<string, scalar|null>,
 * }
 */
final readonly class MultiCheckBox
{
    public const APPEND  = 'append';
    public const PREPEND = 'prepend';

    public function __construct(
        private Escaper $escaper,
        private HtmlAttributes $attributePlugin,
        private Doctype $doctype,
    ) {
    }

    /** @param self::APPEND|self::PREPEND $labelPosition */
    public function __invoke(
        MultiCheckboxElement|Radio $element,
        string $labelPosition = self::APPEND,
    ): string {
        $name = $element->getName();
        if (! is_string($name) || $name === '') {
            throw FormElementsMustBeNamed::with($element);
        }

        $markup = [$this->renderOptions($element, $labelPosition)];

        if ($element->useHiddenElement()) {
            array_unshift(
                $markup,
                $this->hiddenElement($element),
            );
        }

        return implode(PHP_EOL, array_filter($markup));
    }

    /** @return list<OptionSpec> */
    private function normaliseValueOptions(MultiCheckboxElement|Radio $element): array
    {
        $options = [];
        /**
         * Coerce value options into a defined array shape
         */
        foreach ($element->getValueOptions() as $key => $value) {
            if (is_scalar($value)) {
                $options[] = [
                    'value'            => (string) $key,
                    'label'            => $value,
                    'attributes'       => [
                        'value' => (string) $key,
                    ],
                    'label_attributes' => [],
                ];

                continue;
            }

            /** @psalm-suppress DocblockTypeContradiction It is still possible for the value to be invalid */
            if (! is_array($value)) {
                throw MultiCheckBoxCannotBeRendered::becauseOfAnInvalidOptionSpec($value);
            }

            $disabled        = isset($value['disabled']) && $value['disabled'];
            $checked         = isset($value['selected']) && $value['selected'];
            $attributes      = $value['attributes'] ?? [];
            $labelAttributes = $value['label_attributes'] ?? [];

            try {
                Assert::scalar($value['value'] ?? null);
                Assert::scalar($value['label'] ?? null);
                Assert::allNullOrScalar($attributes);
                Assert::allNullOrScalar($labelAttributes);
                Assert::allString(array_keys($attributes));
                Assert::allString(array_keys($labelAttributes));
            } catch (Throwable $e) {
                throw MultiCheckBoxCannotBeRendered::becauseOfAnInvalidOptionSpec($value, $e);
            }

            $attributes['disabled'] = $disabled;
            $attributes['checked']  = $checked;
            $attributes['value']    = $value['value'];

            /** @psalm-var OptionSpec */
            $options[] = [
                'value'            => $value['value'],
                'label'            => $value['label'],
                'attributes'       => $attributes,
                'label_attributes' => $labelAttributes,
            ];
        }

        return $options;
    }

    /** @return list<OptionSpec> */
    private function prepareValueOptions(MultiCheckboxElement|Radio $element): array
    {
        $options = [];
        $name    = $element->getName();
        assert($name !== null && $name !== '');
        if (! str_ends_with($name, '[]') && ! $element instanceof Radio) {
            $name .= '[]';
        }

        $count = 0;

        foreach ($this->normaliseValueOptions($element) as $spec) {
            // Merge in attributes defined on the main element:
            $spec['attributes']         = array_merge($element->getAttributes(), $spec['attributes']);
            $spec['attributes']['name'] = $name;
            $spec['attributes']['type'] = $element instanceof Radio ? 'radio' : 'checkbox';
            // Only the first element should carry the ID, if any.
            if ($count >= 1) {
                unset($spec['attributes']['id']);
            }

            // Override the checked attribute when the element has a non-null value
            if ($element->getValue() !== null) {
                $spec['attributes']['checked'] = $this->isSelected($spec['value'], $element);
            }

            // Merge in label attributes:
            $spec['label_attributes'] = array_merge($element->getLabelAttributes(), $spec['label_attributes']);

            $count++;

            $options[] = $spec;
        }

        return $options;
    }

    /** @param self::APPEND|self::PREPEND $labelPosition */
    private function renderOptions(MultiCheckboxElement|Radio $element, string $labelPosition): string
    {
        $markup = [];

        foreach ($this->prepareValueOptions($element) as $spec) {
            $labelAttributes   = ($this->attributePlugin)(
                AttributeNormaliser::normalise($spec['label_attributes'], new LabelAttribute()),
            );
            $elementAttributes = ($this->attributePlugin)(
                AttributeNormaliser::normalise($spec['attributes'], new InputAttribute()),
            );

            $input = sprintf('<input %s>', $elementAttributes);
            $label = $this->escaper->escapeHtml($spec['label']);

            $markup[] = sprintf(
                '<label%s%s>%s %s</label>',
                $labelAttributes === '' ? '' : ' ',
                $labelAttributes,
                $labelPosition === self::PREPEND ? $label : $input,
                $labelPosition === self::PREPEND ? $input : $label,
            );
        }

        return implode(PHP_EOL, $markup);
    }

    /** @return list<string> */
    private function normaliseSelectedValues(MultiCheckboxElement|Radio $element): array
    {
        $value = $element->getValue();
        if ($value === null || $value === '') {
            return [];
        }

        if (is_scalar($value)) {
            return [(string) $value];
        }

        if (! is_array($value)) {
            throw MultiCheckBoxCannotBeRendered::becauseItIsMultipleAndTheValueIsNotIterable($element);
        }

        return array_values(array_map(static fn (int|float|string $value): string => (string) $value, $value));
    }

    private function isSelected(string $value, MultiCheckboxElement|Radio $element): bool
    {
        return in_array($value, $this->normaliseSelectedValues($element), true);
    }

    private function hiddenElement(Radio|MultiCheckboxElement $element): string
    {
        $attributes = [
            'name' => $element->getName(),
            'type' => 'hidden',
            'value' => $element->getUncheckedValue(),
        ];

        return sprintf(
            '<input %s%s',
            ($this->attributePlugin)($attributes),
            $this->doctype->isXhtml() ? ' />' : '>',
        );
    }
}
