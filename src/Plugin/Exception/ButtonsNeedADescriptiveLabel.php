<?php

declare(strict_types=1);

namespace Looker\Form\Plugin\Exception;

use InvalidArgumentException;
use Laminas\Form\Element\Button;
use Laminas\Form\Element\Submit;

use function sprintf;

final class ButtonsNeedADescriptiveLabel extends InvalidArgumentException
{
    public static function forElement(Button|Submit $element): self
    {
        return new self(sprintf(
            'Buttons should have either a label, or you should pass some content into the plugin. The element named '
            . '"%s" does not have a label and no content was provided',
            $element->getName() ?? 'un-named',
        ));
    }
}
