<?php

declare(strict_types=1);

namespace Looker\Form\HTML;

use Looker\HTML\AttributeInformation;
use Looker\HTML\GlobalAttribute;

use function array_key_exists;
use function in_array;
use function strtolower;

/** @psalm-internal Looker */
final class ButtonAttribute implements AttributeInformation
{
    private const BOOLEAN    = ['disabled', 'formnovalidate'];
    private const STRING     = [
        'autocomplete',
        'form',
        'formaction',
        'formenctype',
        'formmethod',
        'formtarget',
        'name',
        'popovertarget',
        'value',
    ];
    private const ENUMERATED = [
        'popovertargetaction' => ['hide', 'show', 'toggle'],
        'type' => ['submit', 'reset', 'button'],
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
            || self::isBoolean($name)
            || array_key_exists($name, self::ENUMERATED)
            || GlobalAttribute::exists($name);
    }
}
