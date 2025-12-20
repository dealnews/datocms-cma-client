<?php

namespace DealNews\DatoCMS\CMA\DataTypes;

use Moonspot\ValueObjects\Interfaces\Export;

abstract class Common implements \JsonSerializable {

    protected mixed $value = null;

    protected array $localized_values = [];


    public static function init(): static {
        return new static();
    }

    abstract protected function validateValue(mixed $value): void;

    public function set(mixed $value): static {
        $this->validateValue($value);
        $this->value = $value;
        return $this;
    }

    public function addLocale(string $locale, mixed $value): static {
        $this->validateValue($value);
        $this->localized_values[$locale] = $value;
        return $this;
    }

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