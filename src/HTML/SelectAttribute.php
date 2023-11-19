<?php

declare(strict_types=1);

namespace Looker\Form\HTML;

use Looker\HTML\AttributeInformation;
use Looker\HTML\GlobalAttribute;

use function in_array;
use function strtolower;

/** @psalm-internal Looker */
final class SelectAttribute implements AttributeInformation
{
    private const BOOLEAN = ['disabled', 'multiple', 'required'];
    private const STRING  = ['autocomplete', 'form', 'name', 'size'];

    /** @param non-empty-string $name */
    public static function isBoolean(string $name): bool
    {
        $name = strtolower($name);

        return GlobalAttribute::isBoolean($name) || in_array($name, self::BOOLEAN, true);
    }

    /** @param non-empty-string $name */
    public static function exists(string $name): bool
    {
        $name = strtolower($name);

        return in_array($name, self::STRING)
            || self::isBoolean($name)
            || GlobalAttribute::exists($name);
    }
}
