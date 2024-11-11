<?php

declare(strict_types=1);

namespace Looker\Form\Plugin\Exception;

use InvalidArgumentException;

final class FormElementsMustBeNamed extends InvalidArgumentException
{
    public static function forLabelling(): self
    {
        return new self(
            'In order to render a label, the form element must have a non-empty id or name attribute',
        );
    }
}
