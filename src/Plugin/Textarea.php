<?php

declare(strict_types=1);

namespace Looker\Form\Plugin;

use Laminas\Escaper\Escaper;
use Laminas\Form\Element\Textarea as TextareaInput;
use Looker\Form\HTML\TextareaAttribute;
use Looker\Form\Plugin\Exception\FormElementsMustBeNamed;
use Looker\HTML\AttributeNormaliser;
use Looker\Plugin\HtmlAttributes;

use function array_merge;
use function is_string;
use function sprintf;

final readonly class Textarea
{
    public function __construct(
        private Escaper $escaper,
        private HtmlAttributes $attributeHelper,
        private InvalidElementAttributeHandler $invalidElementHandler,
    ) {
    }

    /** @param array<string, scalar|null> $attributes */
    public function __invoke(TextareaInput $element, array $attributes = []): string
    {
        $attributes = array_merge($element->getAttributes(), $attributes);
        $name       = $attributes['name'] ?? null;
        $name       = $element->getName() ?? $name;
        if (! is_string($name) || $name === '') {
            throw FormElementsMustBeNamed::with($element);
        }

        $attributes = ($this->invalidElementHandler)($element, $attributes);
        $attributes = AttributeNormaliser::normalise($attributes, new TextareaAttribute());

        return sprintf(
            '<textarea %s>%s</textarea>',
            $this->attributeHelper->__invoke($attributes),
            $this->escaper->escapeHtml((string) $element->getValue()),
        );
    }
}
