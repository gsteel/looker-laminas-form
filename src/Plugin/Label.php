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

use function array_merge;
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

        return sprintf(
            '%s%s%s',
            $this->openTag($element, $attributes),
            $this->escaper->escapeHtml((string) $element->getLabel()),
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
