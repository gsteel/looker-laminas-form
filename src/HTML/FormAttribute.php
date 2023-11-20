<?php

declare(strict_types=1);

namespace Looker\Form\HTML;

use Looker\HTML\AttributeInformation;
use Looker\HTML\GlobalAttribute;

use function array_key_exists;
use function in_array;
use function strtolower;

final class FormAttribute implements AttributeInformation
{
    private const BOOLEAN    = ['novalidate'];
    private const STRING     = ['accept', 'accept-charset', 'name', 'rel', 'action', 'target'];
    private const ENUMERATED = [
        'autocapitalize' => ['none', 'sentences', 'words', 'characters'],
        'autocomplete' => ['on', 'off'],
        'enctype' => ['application/x-www-form-urlencoded', 'multipart/form-data', 'text/plain'],
        'method' => ['get', 'post', 'dialog'],
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
