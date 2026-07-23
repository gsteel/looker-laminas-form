<?php

declare(strict_types=1);

namespace Looker\Form\Plugin;

use Laminas\Escaper\EscaperInterface;
use Laminas\Form\Element\Select as SelectElement;
use Looker\Form\HTML\OptionAttribute;
use Looker\Form\Plugin\Exception\SelectElementCannotBeRendered;
use Looker\HTML\AttributeNormaliser;
use Looker\Plugin\HtmlAttributes;

use function array_map;
use function array_values;
use function in_array;
use function is_array;
use function is_scalar;
use function sprintf;

final readonly class Option
{
    public function __construct(
        private EscaperInterface $escaper,
        private HtmlAttributes $attributeHelper,
        private AttributeNormaliser $attributeNormaliser,
    ) {
    }

    /** @param array<string, scalar|null> $attributes */
    public function __invoke(SelectElement $element, string $value, string $label, array $attributes): string
    {
        $attributes['value'] = $value;
        unset($attributes['selected']);
        if ($this->isSelected($value, $element)) {
            $attributes['selected'] = true;
        }

        $attributes = $this->attributeNormaliser->normalise($attributes, new OptionAttribute());

        return sprintf(
            '<option %s>%s</option>',
            $this->attributeHelper->__invoke($attributes),
            $this->escaper->escapeHtml($label),
        );
    }

    /**
     * @return list<string>
     * @mago-expect lint:halstead
     */
    private function normaliseSelectedValues(SelectElement $element): array
    {
        $value = $element->getValue();
        if ($value === null || $value === '') {
            return [];
        }

        if (is_scalar($value)) {
            return [(string) $value];
        }

        if (! $element->isMultiple()) {
            throw SelectElementCannotBeRendered::becauseItIsNotMultipleAndTheValueIsNotScalar($element);
        }

        if (! is_array($value)) {
            throw SelectElementCannotBeRendered::becauseItIsMultipleAndTheValueIsNotIterable($element);
        }

        return array_values(array_map(static fn (int|float|string $value): string => (string) $value, $value));
    }

    private function isSelected(string $value, SelectElement $element): bool
    {
        return in_array($value, $this->normaliseSelectedValues($element), true);
    }
}
