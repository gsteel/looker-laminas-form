<?php

declare(strict_types=1);

namespace Looker\Form\Test\Asset;

use Override;
use Stringable;

final readonly class StringableObject implements Stringable
{
    public function __construct(private string $value)
    {
    }

    #[Override]
    public function __toString(): string
    {
        return $this->value;
    }
}
