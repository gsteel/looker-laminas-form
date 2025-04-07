<?php

declare(strict_types=1);

namespace Looker\Form\HTML;

use Looker\HTML\AttributeInformation;
use Looker\HTML\GlobalAttribute;
use Override;

use function in_array;
use function strtolower;

/** @psalm-internal Looker */
final class OptionAttribute implements AttributeInformation
{
    private const array BOOLEAN = ['disabled', 'selected'];
    private const array STRING  = ['value', 'label'];

    /** @param non-empty-string $name */
    #[Override]
    public static function isBoolean(string $name): bool
    {
        $name = strtolower($name);

        return GlobalAttribute::isBoolean($name) || in_array($name, self::BOOLEAN, true);
    }

    /** @param non-empty-string $name */
    #[Override]
    public static function exists(string $name): bool
    {
        $name = strtolower($name);

        return in_array($name, self::STRING)
            || self::isBoolean($name)
            || GlobalAttribute::exists($name);
    }
}
