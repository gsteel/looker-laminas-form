<?php

declare(strict_types=1);

namespace Looker\Form\Plugin\Exception;

use Laminas\Form\Element\MultiCheckbox;
use RuntimeException;
use Throwable;

use function get_debug_type;
use function sprintf;

final class MultiCheckBoxCannotBeRendered extends RuntimeException
{
    public static function becauseItIsMultipleAndTheValueIsNotIterable(MultiCheckbox $element): self
    {
        return new self(sprintf(
            'The selected element named "%s" is a multi-checkbox, but its selected value is not iterable, it’s "%s"',
            (string) $element->getName(),
            get_debug_type($element->getValue()),
        ));
    }

    public static function becauseOfAnInvalidOptionSpec(mixed $optionSpec, Throwable|null $previous = null): self
    {
        return new self(sprintf(
            'Value options should be simple scalar key-value pairs, or a specification that conforms to the following:'
            . <<<'SPEC'
            array{
              value: scalar,
              label: scalar,
              disabled?: bool,
              selected?: bool,
              attributes?: array<string, scalar>,
              label_attributes?: array<string, scalar>,
            }
            SPEC . 'but… %s was received',
            get_debug_type($optionSpec),
        ), 0, $previous);
    }
}
