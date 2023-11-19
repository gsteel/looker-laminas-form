<?php

declare(strict_types=1);

namespace Looker\Form\Plugin;

use Laminas\Escaper\Escaper;
use Laminas\Form\ElementInterface;
use Laminas\Form\Fieldset as FormFieldset;
use Looker\Form\HTML\FieldsetAttribute;
use Looker\HTML\AttributeNormaliser;
use Looker\HTML\GlobalAttribute;
use Looker\Plugin\HtmlAttributes;

use function array_merge;
use function assert;
use function implode;
use function sprintf;
use function trim;

use const PHP_EOL;

final readonly class Fieldset
{
    public function __construct(
        private Escaper $escaper,
        private HtmlAttributes $attributeHelper,
        private FormElementRow $elementHelper,
    ) {
    }

    /** @param array<string, scalar|null> $attributes */
    public function __invoke(FormFieldset $fieldset, array $attributes = []): string
    {
        if ($fieldset->count() === 0) {
            return '';
        }

        $fieldsetContent = trim(sprintf(
            '%s%s%s',
            $fieldset->useAsBaseFieldset() ? '' : $this->legend($fieldset),
            PHP_EOL,
            $this->elements($fieldset),
        ));

        if ($fieldset->useAsBaseFieldset()) {
            return $fieldsetContent;
        }

        $attributes = ($this->attributeHelper)(
            AttributeNormaliser::normalise(
                array_merge($fieldset->getAttributes(), $attributes),
                new FieldsetAttribute(),
            ),
        );

        return sprintf(
            <<<'HTML'
            <fieldset%s>
            %s
            </fieldset>
            HTML,
            $attributes === '' ? '' : ' ' . $attributes,
            $fieldsetContent,
        );
    }

    private function legend(FormFieldset $fieldset): string
    {
        $label = (string) $fieldset->getLabel();
        if ($fieldset->useAsBaseFieldset() || $label === '') {
            return '';
        }

        $attributes = ($this->attributeHelper)(
            AttributeNormaliser::normalise(
                $fieldset->getLabelAttributes(),
                new GlobalAttribute(),
            ),
        );

        return sprintf(
            '<legend%s>%s</legend>',
            $attributes === '' ? '' : ' ' . $attributes,
            $this->escaper->escapeHtml($label),
        );
    }

    private function elements(FormFieldset $fieldset): string
    {
        $buffer = [];
        foreach ($fieldset as $element) {
            if ($element instanceof FormFieldset) {
                $buffer[] = ($this)($element);
                continue;
            }

            assert($element instanceof ElementInterface);

            $buffer[] = ($this->elementHelper)($element);
        }

        return implode(PHP_EOL, $buffer);
    }
}
