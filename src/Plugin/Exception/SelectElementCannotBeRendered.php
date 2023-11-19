<?php

declare(strict_types=1);

namespace Looker\Form\Plugin\Exception;

use Laminas\Form\Element\Select;
use RuntimeException;

use function get_debug_type;
use function sprintf;

final class SelectElementCannotBeRendered extends RuntimeException
{
    public static function becauseItIsNotMultipleAndTheValueIsNotScalar(Select $element): self
    {
        return new self(sprintf(
            'The selected element named "%s" is not a multi-select and it’s selected value is not scalar, it’s "%s"',
            (string) $element->getName(),
            get_debug_type($element->getValue()),
        ));
    }

    public static function becauseItIsMultipleAndTheValueIsNotIterable(Select $element): self
    {
        return new self(sprintf(
            'The selected element named "%s" is a multi-select but it’s selected value is not iterable, it’s "%s"',
            (string) $element->getName(),
            get_debug_type($element->getValue()),
        ));
    }

    public static function becauseOptionsCannotBeCoerced(Select $element): self
    {
        return new self(sprintf(
            'Select value options must be key-value pairs, a spec that represents an optgroup, or a spec that '
            . 'represents an option, but invalid values were found for the element named "%s"',
            (string) $element->getName(),
        ));
    }
}
