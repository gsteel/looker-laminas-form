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
                Plugin\ElementErrorList::class => Plugin\Factory\ElementErrorListFactory::class,
                Plugin\Fieldset::class => Plugin\Factory\FieldsetFactory::class,
                Plugin\Form::class => Plugin\Factory\FormFactory::class,
                Plugin\FormElement::class => Plugin\Factory\FormElementFactory::class,
                Plugin\FormElementRow::class => Plugin\Factory\FormElementRowFactory::class,
                Plugin\FormInput::class => Plugin\Factory\FormInputFactory::class,
                Plugin\InvalidElementAttributeHandler::class => Plugin\Factory\InvalidElementAttributeHandlerFactory::class,
                Plugin\Label::class => Plugin\Factory\LabelFactory::class,
                Plugin\Option::class => Plugin\Factory\OptionFactory::class,
                Plugin\Select::class => Plugin\Factory\SelectFactory::class,
                Plugin\Textarea::class => Plugin\Factory\TextareaFactory::class,
            ],
            'aliases' => [
                'formInput' => Plugin\FormInput::class,
                'formLabel' => Plugin\Label::class,
            ],
        ];
    }
}
