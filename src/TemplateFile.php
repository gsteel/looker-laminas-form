<?php

declare(strict_types=1);

namespace Looker\Form;

use Laminas\Form\Element\Button as ButtonElement;
use Laminas\Form\Element\Checkbox as CheckboxElement;
use Laminas\Form\Element\Color;
use Laminas\Form\Element\Date;
use Laminas\Form\Element\DateTimeLocal;
use Laminas\Form\Element\Email;
use Laminas\Form\Element\File;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Image;
use Laminas\Form\Element\Month;
use Laminas\Form\Element\MultiCheckbox as MultiCheckboxElement;
use Laminas\Form\Element\Number;
use Laminas\Form\Element\Password;
use Laminas\Form\Element\Radio;
use Laminas\Form\Element\Range;
use Laminas\Form\Element\Search;
use Laminas\Form\Element\Submit;
use Laminas\Form\Element\Tel;
use Laminas\Form\Element\Text;
use Laminas\Form\Element\Textarea as TextareaInput;
use Laminas\Form\Element\Time;
use Laminas\Form\Element\Url;
use Laminas\Form\Element\Week;
use Laminas\Form\ElementInterface;
use Laminas\Form\FormInterface;
use Looker\Form\Plugin\Form;
use Looker\Form\Plugin\FormElementRow;
use Looker\Form\Plugin\Label;
use Looker\Form\Plugin\MultiCheckBox;
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
     * @return ($form is null ? Form : string)
     */
    public function form(FormInterface|null $form = null, array $attributes = []): string|Form;

    /**
     * @param non-empty-string|null      $buttonContent
     * @param array<string, scalar|null> $attributes
     */
    public function formButton(
        ButtonElement|Submit $element,
        string|null $buttonContent = null,
        bool $escapeContent = true,
        array $attributes = [],
    ): string;

    /** @param array<string, scalar|null> $attributes */
    public function formCheckbox(
        CheckboxElement $element,
        array $attributes = [],
    ): string;

    /** @param array<string, scalar|null> $attributes */
    public function formColor(Color $element, array $attributes = []): string;

    /** @param array<string, scalar|null> $attributes */
    public function formDate(Date $element, array $attributes = []): string;

    /** @param array<string, scalar|null> $attributes */
    public function formDateTimeLocal(DateTimeLocal $element, array $attributes = []): string;

    /** @param array<string, scalar|null> $attributes */
    public function formElement(ElementInterface $element, array $attributes = []): string;

    /** @param array<string, scalar|null> $listAttributes */
    public function formElementErrors(ElementInterface $element, array $listAttributes = []): string;

    /**
     * @param array<string, scalar|null>                     $elementAttributes
     * @param array<string, scalar|null>                     $labelAttributes
     * @param array<string, scalar|null>                     $errorListAttributes
     * @param array<string, scalar|null>                     $wrapperAttributes
     * @param FormElementRow::APPEND|FormElementRow::PREPEND $labelPosition
     * @param FormElementRow::APPEND|FormElementRow::PREPEND $errorPosition
     */
    public function formElementRow(
        ElementInterface $element,
        array $elementAttributes = [],
        array $labelAttributes = [],
        array $errorListAttributes = [],
        array $wrapperAttributes = [],
        string $labelPosition = FormElementRow::PREPEND,
        string $errorPosition = FormElementRow::APPEND,
        bool $labelWrap = false,
    ): string;

    /** @param array<string, scalar|null> $attributes */
    public function formEmail(Email $element, array $attributes = []): string;

    public function formFieldset(): string;

    /** @param array<string, scalar|null> $attributes */
    public function formFile(File $element, array $attributes = []): string;

    /** @param array<string, scalar|null> $attributes */
    public function formHidden(Hidden $element, array $attributes = []): string;

    /** @param array<string, scalar|null> $attributes */
    public function formImage(Image $element, array $attributes = []): string;

    /** @param array<string, scalar|null> $attributes */
    public function formInput(ElementInterface $element, array $attributes = []): string;

    /**
     * @param array<string, scalar|null> $attributes
     *
     * @psalm-return ($element is ElementInterface ? string : Label)
     */
    public function formLabel(ElementInterface|null $element = null, array $attributes = []): Label|string;

    /** @param array<string, scalar|null> $attributes */
    public function formMonth(Month $element, array $attributes = []): string;

    /** @param MultiCheckBox::APPEND|MultiCheckBox::PREPEND $labelPosition */
    public function formMultiCheckbox(
        MultiCheckboxElement|Radio $element,
        string $labelPosition = MultiCheckBox::APPEND,
    ): string;

    /** @param array<string, scalar|null> $attributes */
    public function formNumber(Number $element, array $attributes = []): string;

    /** @param array<string, scalar|null> $attributes */
    public function formPassword(Password $element, array $attributes = []): string;

    /** @param MultiCheckBox::APPEND|MultiCheckBox::PREPEND $labelPosition */
    public function formRadio(
        MultiCheckboxElement|Radio $element,
        string $labelPosition = MultiCheckBox::APPEND,
    ): string;

    /** @param array<string, scalar|null> $attributes */
    public function formRange(Range $element, array $attributes = []): string;

    /** @param array<string, scalar|null> $attributes */
    public function formSearch(Search $element, array $attributes = []): string;

    /**
     * @param non-empty-string|null      $buttonContent
     * @param array<string, scalar|null> $attributes
     */
    public function formSubmit(
        Submit $element,
        string|null $buttonContent = null,
        bool $escapeContent = true,
        array $attributes = [],
    ): string;

    /** @param array<string, scalar|null> $attributes */
    public function formTel(Tel $element, array $attributes = []): string;

    /** @param array<string, scalar|null> $attributes */
    public function formText(Text $element, array $attributes = []): string;

    /** @param array<string, scalar|null> $attributes */
    public function formTextarea(TextareaInput $element, array $attributes = []): string;

    /** @param array<string, scalar|null> $attributes */
    public function formTime(Time $element, array $attributes = []): string;

    /** @param array<string, scalar|null> $attributes */
    public function formUrl(Url $element, array $attributes = []): string;

    /** @param array<string, scalar|null> $attributes */
    public function formWeek(Week $element, array $attributes = []): string;
}
