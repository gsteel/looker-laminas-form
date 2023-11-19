<?php

declare(strict_types=1);

namespace Looker\Form\HTML;

use Looker\HTML\AttributeInformation;
use Looker\HTML\GlobalAttribute;

use function array_key_exists;
use function in_array;
use function strtolower;

/** @psalm-internal Looker */
final class TextareaAttribute implements AttributeInformation
{
    private const STRING     = ['cols', 'maxlength', 'minlength', 'name', 'placeholder', 'rows'];
    private const BOOLEAN    = ['disabled', 'readonly', 'required'];
    private const ENUMERATED = [
        'autocomplete' => ['on', 'off'],
        'autocorrect' => ['on', 'off'],
        'dirname' => ['ltr', 'rtl'],
        'wrap' => ['hard', 'soft', 'off'],
    ];

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
            || array_key_exists($name, self::ENUMERATED)
            || self::isBoolean($name)
            || GlobalAttribute::exists($name);
    }
}
