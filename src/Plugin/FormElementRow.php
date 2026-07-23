<?php

declare(strict_types=1);

namespace Looker\Form\Plugin;

use Laminas\Escaper\EscaperInterface;
use Laminas\Form\ElementInterface;
use Laminas\Form\Fieldset as FormFieldset;
use Looker\HTML\AttributeNormaliser;
use Looker\HTML\GlobalAttribute;
use Looker\Plugin\HtmlAttributes;

use function array_filter;
use function array_unshift;
use function implode;
use function sprintf;

use const PHP_EOL;

final readonly class FormElementRow
{
    public const string PREPEND = 'prepend';
    public const string APPEND = 'append';

    public function __construct(
        private EscaperInterface $escaper,
        private Label $labelPlugin,
        private ElementErrorList $errorListPlugin,
        private FormElement $elementPlugin,
        private HtmlAttributes $attributePlugin,
        private AttributeNormaliser $attributeNormaliser,
    ) {
    }

    /**
     * @param array<string, scalar|null> $elementAttributes
     * @param array<string, scalar|null> $labelAttributes
     * @param array<string, scalar|null> $errorListAttributes
     * @param array<string, scalar|null> $wrapperAttributes
     * @param self::APPEND|self::PREPEND $labelPosition
     * @param self::APPEND|self::PREPEND $errorPosition
     * @mago-expect lint:excessive-parameter-list
     */
    public function __invoke(
        ElementInterface $element,
        array $elementAttributes = [],
        array $labelAttributes = [],
        array $errorListAttributes = [],
        array $wrapperAttributes = [],
        string $labelPosition = self::PREPEND,
        string $errorPosition = self::APPEND,
        bool $labelWrap = false,
    ): string {
        $elementMarkup = ($this->elementPlugin)($element, $elementAttributes);

        if ($element instanceof FormFieldset) {
            return $elementMarkup;
        }

        $labelOpenTag = $this->labelPlugin->openTag($element, $labelAttributes);
        $label = $this->escaper->escapeHtml((string) $element->getLabel());
        $labelCloseTag = $this->labelPlugin->closeTag($element);
        $errorList = ($this->errorListPlugin)($element, $errorListAttributes);

        $markup = [
            $labelOpenTag,
            $label,
            $labelCloseTag,
            $errorPosition === self::APPEND ? $elementMarkup : $errorList,
            $errorPosition === self::APPEND ? $errorList : $elementMarkup,
        ];

        if ($labelWrap) {
            $markup = [
                $labelOpenTag,
                $labelPosition === self::APPEND ? $elementMarkup : $label,
                $labelPosition === self::APPEND ? $label : $elementMarkup,
                $labelCloseTag,
                $errorList,
            ];
        }

        $wrapperAttributes = ($this->attributePlugin)(
            $this->attributeNormaliser->normalise(
                $wrapperAttributes,
                new GlobalAttribute(),
            ),
        );

        array_unshift(
            $markup,
            sprintf('<div%s>', $wrapperAttributes === '' ? '' : ' ' . $wrapperAttributes),
        );

        $markup[] = '</div>';

        return implode(PHP_EOL, array_filter($markup));
    }
}
