<?php

declare(strict_types=1);

namespace Looker\Form\Plugin;

use Laminas\Escaper\Escaper;
use Laminas\Form\ElementInterface;
use Looker\Plugin\HtmlAttributes;

use function array_map;
use function array_merge;
use function array_walk_recursive;
use function implode;
use function sprintf;

use const PHP_EOL;

final readonly class ElementErrorList
{
    /** @param array<string, scalar|null> $defaultAttributes */
    public function __construct(
        private Escaper $escaper,
        private HtmlAttributes $attributeHelper,
        private array $defaultAttributes = [],
    ) {
    }

    /** @param array<string, scalar|null> $listAttributes */
    public function __invoke(ElementInterface $element, array $listAttributes = []): string
    {
        $messages = $element->getMessages();
        $list     = [];
        array_walk_recursive($messages, static function (string $message) use (&$list): void {
            $list[] = $message;
        });

        if ($list === []) {
            return '';
        }

        $attributes = ($this->attributeHelper)(array_merge($this->defaultAttributes, $listAttributes));

        /**
         * The list type needs forcing because array_walk_recursive with the by-ref value cannot be inferred
         * @psalm-var list<non-empty-string> $list
         */

        return sprintf(
            '<ul%1$s>%2$s%3$s%2$s</ul>',
            $attributes === '' ? '' : ' ' . $attributes,
            PHP_EOL,
            implode(PHP_EOL, array_map(
                fn (string $message): string => sprintf('<li>%s</li>', $this->escaper->escapeHtml($message)),
                $list,
            )),
        );
    }
}
