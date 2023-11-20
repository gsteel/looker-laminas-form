<?php

declare(strict_types=1);

namespace Looker\Form\Plugin;

use Laminas\Form\Form as LaminasForm;
use Laminas\Form\FormInterface;
use Looker\Form\HTML\FormAttribute;
use Looker\HTML\AttributeNormaliser;
use Looker\Plugin\HtmlAttributes;

use function array_filter;
use function array_merge;
use function implode;
use function sprintf;

use const PHP_EOL;

final readonly class Form
{
    public function __construct(
        private HtmlAttributes $attributePlugin,
        private FormElementRow $elementHelper,
    ) {
    }

    /**
     * @param array<string, scalar|null> $attributes
     *
     * @return ($form is null ? self : string)
     */
    public function __invoke(FormInterface|null $form = null, array $attributes = []): string|self
    {
        if ($form === null) {
            return $this;
        }

        $markup = [$this->openTag($form, $attributes)];
        foreach ($form as $element) {
            $markup[] = ($this->elementHelper)($element);
        }

        $markup[] = $this->closeTag();

        return implode(PHP_EOL, array_filter($markup));
    }

    /** @param array<string, scalar|null> $attributes */
    public function openTag(FormInterface $form, array $attributes = []): string
    {
        if ($form instanceof LaminasForm) {
            $form->prepare();
        }

        $attributes = ($this->attributePlugin)(
            AttributeNormaliser::normalise(
                array_merge($form->getAttributes(), $attributes),
                new FormAttribute(),
            ),
        );

        return sprintf(
            '<form%s%s>',
            $attributes === '' ? '' :  ' ',
            $attributes,
        );
    }

    public function closeTag(): string
    {
        return '</form>';
    }
}
