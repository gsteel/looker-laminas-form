<?php

declare(strict_types=1);

namespace Looker\Form\HTML;

use Looker\HTML\AttributeInformation;
use Looker\HTML\GlobalAttribute;

use function array_key_exists;
use function in_array;
use function strtolower;

/** @psalm-internal Looker */
final class InputAttribute implements AttributeInformation
{
    private const STRING = [
        'accept',
        'alt',
        'autocomplete', // Actually an enumeration, but values can be combined
        'form',
        'formaction',
        'formenctype',
        'formmethod',
        'formtarget',
        'height',
        'list',
        'max',
        'maxlength',
        'min',
        'minlength',
        'name',
        'pattern',
        'placeholder',
        'popovertarget',
        'popovertargetaction',
        'size',
        'src',
        'step',
        'value',
        'width',
    ];

    private const BOOLEAN = [
        'disabled',
        'checked',
        'formnovalidate',
        'multiple',
        'readonly',
        'required',
    ];

    private const ENUMERATED = [
        'type' => [
            'button',
            'checkbox',
            'color',
            'date',
            'datetime-local',
            'email',
            'file',
            'hidden',
            'image',
            'month',
            'number',
            'password',
            'radio',
            'range',
            'reset',
            'search',
            'submit',
            'tel',
            'text',
            'time',
            'url',
            'week',
        ],
        'dirname' => ['ltr', 'rtl'],
        'capture' => ['user', 'environment'],
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
