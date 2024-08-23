<?php

declare(strict_types=1);

namespace Looker\Form\Plugin;

use Laminas\Escaper\Escaper;
use Laminas\Form\ElementInterface;
use Laminas\Form\LabelAwareInterface;
use Looker\Form\HTML\LabelAttribute;
use Looker\Form\Plugin\Exception\FormElementsMustBeNamed;
use Looker\HTML\AttributeNormaliser;
use Looker\Plugin\HtmlAttributes;

use function array_key_exists;
use function array_merge;
use function is_bool;
use function is_string;
use function sprintf;

final readonly class Label
{
    public function __construct(
        private Escaper $escaper,
        private HtmlAttributes $attributeHelper,
    ) {
    }

    /**
     * @param array<string, scalar|null> $attributes
     *
     * @psalm-return ($element is ElementInterface ? string : self)
     */
    public function __invoke(ElementInterface|null $element = null, array $attributes = []): string|self
    {
        if (! $element) {
            return $this;
        }

        $escape = true;
        if ($element instanceof LabelAwareInterface) {
            $labelOptions = $element->getLabelOptions();
            // Laminas Form has historically used this option name for disabling escape of labels:
            if (array_key_exists('disable_html_escape', $labelOptions)) {
                $escape = $labelOptions['disable_html_escape'] !== true;
            }

            // This is an alternative way of enabling/disabling the escape option:
            if (array_key_exists('escape', $labelOptions)) {
                $escape = is_bool($labelOptions['escape']) ? $labelOptions['escape'] : $escape;
            }
        }

        return sprintf(
            '%s%s%s',
            $this->openTag($element, $attributes),
            $escape
                ? $this->escaper->escapeHtml((string) $element->getLabel())
                : (string) $element->getLabel(),
            $this->closeTag($element),
        );
    }

    /** @param array<string, scalar|null> $attributes */
    public function openTag(ElementInterface $element, array $attributes = []): string
    {
        if ($element->getLabel() === null || $element->getLabel() === '') {
            return '';
        }

        if ($element instanceof LabelAwareInterface) {
            $attributes = array_merge($element->getLabelAttributes(), $attributes);
        }

        $id = $element->getAttribute('id') ?? null;
        $id = is_string($id) ? $id : $element->getName();
        /** @psalm-var mixed $id */
        $id = $attributes['for'] ?? $id;
        if (! is_string($id) || $id  === '') {
            throw FormElementsMustBeNamed::forLabelling();
        }

        $attributes['for'] = $id;

        $attributes = AttributeNormaliser::normalise($attributes, new LabelAttribute());

        return sprintf(
            '<label %s>',
            $this->attributeHelper->__invoke($attributes),
        );
    }

    public function closeTag(ElementInterface $element): string
    {
        if ($element->getLabel() === null || $element->getLabel() === '') {
            return '';
        }

        return '</label>';
    }
}
