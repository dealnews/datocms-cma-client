<?php

namespace DealNews\DatoCMS\CMA\DataTypes;

use Moonspot\ValueObjects\Interfaces\Export;

/**
 * Abstract base class for DatoCMS field data types
 *
 * Provides common functionality for all DatoCMS field types including
 * single-value storage, localized value support, and JSON serialization.
 * Subclasses must implement validateValue() for type-specific validation.
 *
 * Usage:
 * ```php
 * $scalar = Scalar::init()->set('Hello World');
 * $localized = Scalar::init()
 *     ->addLocale('en', 'Hello')
 *     ->addLocale('es', 'Hola');
 * ```
 *
 * @suppress PhanRedefinedInheritedInterface
 */
abstract class Common implements \JsonSerializable {

    /**
     * Non-localized value storage
     *
     * @var mixed
     */
    protected mixed $value = null;

    /**
     * Localized values keyed by locale code
     *
     * @var array<string, mixed>
     */
    protected array $localized_values = [];


    /**
     * Factory method for creating a new instance
     *
     * @return static New instance of the concrete class
     */
    public static function init(): static {
        return new static();
    }

    /**
     * Validates the provided value against type-specific rules
     *
     * @param mixed $value Value to validate
     *
     * @return void
     *
     * @throws \InvalidArgumentException If validation fails
     */
    abstract protected function validateValue(mixed $value): void;

    /**
     * Sets the non-localized value
     *
     * @param mixed $value Value to set
     *
     * @return static This instance for method chaining
     *
     * @throws \InvalidArgumentException If validation fails
     */
    public function set(mixed $value): static {
        $this->validateValue($value);
        $this->value = $value;
        return $this;
    }

    /**
     * Adds a localized value for a specific locale
     *
     * @param string $locale Locale code (e.g., 'en', 'es', 'fr')
     * @param mixed  $value  Value for this locale
     *
     * @return static This instance for method chaining
     *
     * @throws \InvalidArgumentException If validation fails
     */
    public function addLocale(string $locale, mixed $value): static {
        $this->validateValue($value);
        $this->localized_values[$locale] = $value;
        return $this;
    }

    /**
     * Serializes the value for JSON encoding
     *
     * Returns localized values if set, otherwise returns the non-localized value.
     * Handles Export and JsonSerializable objects within values.
     *
     * @return mixed Serialized value, localized array, or null if empty
     *
     * @throws \LogicException If value is an object without Export/JsonSerializable
     */
    public function jsonSerialize(): mixed {
        if (!empty($this->localized_values)) {
            $copy = $this->localized_values;
            foreach ($this->localized_values as $locale => $value) {
                if (is_object($value)) {
                    if ($value instanceof Export) {
                        $copy[$locale] = $value->toArray();
                    } elseif ($value instanceof \JsonSerializable) {
                        $copy[$locale] = $value->jsonSerialize();
                    } else {
                        throw new \LogicException("Locale $locale does not implement the Export or JsonSerializable interface");
                    }
                }
            }
            return $copy;
        } elseif (!empty($this->value)) {
            if (is_object($this->value)) {
                if ($this->value instanceof Export) {
                    return $this->value->toArray();
                } elseif ($this->value instanceof \JsonSerializable) {
                    return $this->value->jsonSerialize();
                } else {
                    throw new \LogicException("Value is an object and does not implement the Export or JsonSerializable interface");
                }
            } else {
                return $this->value;
            }
        }

        return null;
    }
}