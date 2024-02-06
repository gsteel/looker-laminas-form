<?php

declare(strict_types=1);

namespace Looker\Form\Plugin;

use Laminas\Escaper\Escaper;
use Laminas\Form\Element\Button as ButtonElement;
use Laminas\Form\Element\Submit;
use Looker\Form\HTML\ButtonAttribute;
use Looker\Form\Plugin\Exception\ButtonsNeedADescriptiveLabel;
use Looker\HTML\AttributeNormaliser;
use Looker\Plugin\HtmlAttributes;

use function array_merge;
use function is_string;
use function sprintf;

final class Button
{
    public function __construct(
        private Escaper $escaper,
        private HtmlAttributes $attributesHelper,
    ) {
    }

    /**
     * @param non-empty-string|null      $buttonContent
     * @param array<string, scalar|null> $attributes
     */
    public function __invoke(
        ButtonElement|Submit $element,
        string|null $buttonContent = null,
        bool $escapeContent = true,
        array $attributes = [],
    ): string {
        $attributes = array_merge($element->getAttributes(), $attributes);

        if ($element->getValue() !== null) {
            $attributes['value'] = (string) $element->getValue();
        }

        if ($element instanceof Submit) {
            $attributes['type'] = 'submit';
        }

        $buttonContent ??= $element->getLabel();

        if (! is_string($buttonContent) || $buttonContent === '') {
            throw ButtonsNeedADescriptiveLabel::forElement($element);
        }

        $attributes = AttributeNormaliser::normalise($attributes, new ButtonAttribute());

        return sprintf(
            '<button %s>%s</button>',
            ($this->attributesHelper)($attributes),
            $escapeContent ? $this->escaper->escapeHtml($buttonContent) : $buttonContent,
        );
    }
}
