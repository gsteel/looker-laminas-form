<?php

declare(strict_types=1);

namespace Looker\Form;

use Laminas\Form\ElementInterface;
use Looker\Form\Plugin\Label;
use Looker\TemplateFile as BaseIDEHelpers;

/**
 * Template File Auto-completion Helper Interface
 *
 * This interface should not be implemented. Its sole purpose is to provide an easy way for IDE's to provide
 * autocompletion and static type checks for plugin methods used in your template files.
 *
 * Within your template file, add a docblock annotation casting $this to the \Looker\Form\FormTemplate type and your
 * IDE should do the rest of the work.
 *
 * If you write custom plugins, you can extend this template in your own projects
 *
 * @psalm-suppress PossiblyUnusedMethod, UnusedClass
 */
interface TemplateFile extends BaseIDEHelpers
{
    /**
     * @param array<string, scalar|null> $attributes
     *
     * @psalm-return ($element is ElementInterface ? string : Label)
     */
    public function formLabel(ElementInterface|null $element = null, array $attributes = []): Label|string;
}
