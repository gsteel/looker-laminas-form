<?php

declare(strict_types=1);

namespace Looker\Form;

/** phpcs:disable Generic.Files.LineLength.TooLong, Squiz.Arrays.ArrayDeclaration.MultiLineNotAllowed */
final class ConfigProvider
{
    /** @return array<string, mixed> */
    public function __invoke(): array
    {
        return [
            'looker' => [
                'plugins' => $this->pluginDependencies(),
                'pluginConfig' => [
                    'formElementErrorListAttributes' => [
                        'class' => 'error-list',
                    ],
                    /**
                     * Add static functions to manipulate the attributes of form elements when they are in an
                     * invalid state. Callable signature is `callable(array): array`
                     */
                    'invalidElementAttributeHandlers' => [],
                ],
            ],
        ];
    }

    /** @return array{factories: array<class-string, class-string>, aliases: array<string, class-string>} */
    private function pluginDependencies(): array
    {
        return [
            'factories' => [
                Plugin\Button::class => Plugin\Factory\ButtonFactory::class,
                Plugin\CheckBox::class => Plugin\Factory\CheckBoxFactory::class,
                Plugin\ElementErrorList::class => Plugin\Factory\ElementErrorListFactory::class,
                Plugin\Fieldset::class => Plugin\Factory\FieldsetFactory::class,
                Plugin\Form::class => Plugin\Factory\FormFactory::class,
                Plugin\FormElement::class => Plugin\Factory\FormElementFactory::class,
                Plugin\FormElementRow::class => Plugin\Factory\FormElementRowFactory::class,
                Plugin\FormInput::class => Plugin\Factory\FormInputFactory::class,
                Plugin\InvalidElementAttributeHandler::class => Plugin\Factory\InvalidElementAttributeHandlerFactory::class,
                Plugin\Label::class => Plugin\Factory\LabelFactory::class,
                Plugin\MultiCheckBox::class => Plugin\Factory\MultiCheckBoxFactory::class,
                Plugin\Option::class => Plugin\Factory\OptionFactory::class,
                Plugin\Select::class => Plugin\Factory\SelectFactory::class,
                Plugin\Textarea::class => Plugin\Factory\TextareaFactory::class,
            ],
            'aliases' => [
                'form' => Plugin\Form::class,
                'formButton' => Plugin\Button::class,
                'formCheckbox' => Plugin\CheckBox::class,
                'formColor' => Plugin\FormInput::class,
                'formDate' => Plugin\FormInput::class,
                'formDateTimeLocal' => Plugin\FormInput::class,
                'formElement' => Plugin\FormElement::class,
                'formElementErrors' => Plugin\ElementErrorList::class,
                'formElementRow' => Plugin\FormElementRow::class,
                'formEmail' => Plugin\FormInput::class,
                'formFieldset' => Plugin\Fieldset::class,
                'formFile' => Plugin\FormInput::class,
                'formHidden' => Plugin\FormInput::class,
                'formImage' => Plugin\FormInput::class,
                'formInput' => Plugin\FormInput::class,
                'formLabel' => Plugin\Label::class,
                'formMonth' => Plugin\FormInput::class,
                'formMultiCheckbox' => Plugin\MultiCheckBox::class,
                'formNumber' => Plugin\FormInput::class,
                'formPassword' => Plugin\FormInput::class,
                'formRadio' => Plugin\MultiCheckBox::class,
                'formRange' => Plugin\FormInput::class,
                'formSearch' => Plugin\FormInput::class,
                'formSubmit' => Plugin\Button::class,
                'formTel' => Plugin\FormInput::class,
                'formText' => Plugin\FormInput::class,
                'formTextarea' => Plugin\Textarea::class,
                'formTime' => Plugin\FormInput::class,
                'formUrl' => Plugin\FormInput::class,
                'formWeek' => Plugin\FormInput::class,
            ],
        ];
    }
}
