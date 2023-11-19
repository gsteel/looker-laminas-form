<?php

declare(strict_types=1);

namespace Looker\Form\Plugin\Exception;

use InvalidArgumentException;
use Laminas\Form\ElementInterface;

use function sprintf;

final class InvalidElementType extends InvalidArgumentException
{
    /** @param class-string $pluginClass */
    public static function becauseOfUnHandledType(ElementInterface $element, string $pluginClass): self
    {
        return new self(sprintf(
            'The plugin "%s" cannot handle form elements of the type "%s"',
            $pluginClass,
            $element::class,
        ));
    }
}
