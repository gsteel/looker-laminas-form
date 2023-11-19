<?php

declare(strict_types=1);

namespace Looker\Form\Plugin\Exception;

use InvalidArgumentException;
use Laminas\Form\ElementInterface;

use function sprintf;

final class FormElementsMustBeNamed extends InvalidArgumentException
{
    public static function with(ElementInterface $element): self
    {
        return new self(sprintf(
            'Received a form element of type "%s" that does not have a name, nor any name attribute provided',
            $element::class,
        ));
    }

    public static function forLabelling(): self
    {
        return new self(
            'In order to render a label, the form element must have a non-empty id or name attribute',
        );
    }
}
