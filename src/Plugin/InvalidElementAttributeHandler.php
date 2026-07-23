<?php

declare(strict_types=1);

namespace Looker\Form\Plugin;

use Laminas\Form\ElementInterface;

use function array_values;

/** @psalm-type CallableSpec = callable(array<string, scalar|null>): array<string, scalar|null> */
final class InvalidElementAttributeHandler
{
    /** @var list<CallableSpec> */
    private array $handlers;

    /** @param CallableSpec ...$handlers */
    public function __construct(callable ...$handlers)
    {
        $this->handlers = array_values($handlers);

        if ($handlers !== []) {
            return;
        }

        /** @psalm-var CallableSpec $handler */
        $handler =
            /**
             * @param array<string, scalar|null> $attributes
             *
             * @return array<string, scalar|null>
             */
            static function (array $attributes): array {
                $attributes['aria-invalid'] = 'true';

                return $attributes;
            };

        $this->handlers = [$handler];
    }

    /**
     * @param array<string, scalar|null> $attributes
     *
     * @return array<string, scalar|null>
     */
    public function __invoke(ElementInterface $element, array $attributes): array
    {
        /**
         * Laminas will not throw in this situation
         *
         * @mago-expect analysis:unhandled-thrown-type
         */
        if ($element->getMessages() === []) {
            return $attributes;
        }

        foreach ($this->handlers as $handler) {
            $attributes = $handler($attributes);
        }

        return $attributes;
    }
}
