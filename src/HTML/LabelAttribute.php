<?php

declare(strict_types=1);

namespace Looker\Form\HTML;

use Looker\HTML\AttributeInformation;
use Looker\HTML\GlobalAttribute;

use function in_array;
use function strtolower;

/** @psalm-internal Looker */
final class LabelAttribute implements AttributeInformation
{
    private const STRING = ['for'];

    /** @param non-empty-string $name */
    public static function isBoolean(string $name): bool
    {
        return GlobalAttribute::isBoolean($name);
    }

    /** @param non-empty-string $name */
    public static function exists(string $name): bool
    {
        $name = strtolower($name);

        return in_array($name, self::STRING)
            || GlobalAttribute::exists($name);
    }
}
