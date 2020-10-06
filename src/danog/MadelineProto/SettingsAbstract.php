<?php

namespace danog\MadelineProto;

use ReflectionClass;
use ReflectionProperty;

abstract class SettingsAbstract
{
    /**
     * Whether this setting was changed.
     *
     * @var boolean
     */
    protected $changed = true;
    /**
     * Merge legacy settings array.
     *
     * @param array $settings Settings array
     *
     * @return void
     */
    public function mergeArray(array $settings): void
    {
    }

    /**
     * Merge with other settings instance.
     *
     * @param self $other
     *
     * @return void
     */
    public function merge(self $other): void
    {
        $class = new ReflectionClass($other);
        foreach ($class->getProperties(ReflectionProperty::IS_PROTECTED|ReflectionProperty::IS_PUBLIC) as $property) {
            $name = $property->getName();
            if ($name === 'changed') {
                continue;
            }
            $uc = \ucfirst($name);
            if (
                isset($other->{$name})
                && $other->{$name} !== $this->{$name}
            ) {
                $this->{"set$uc"}($other->{$name});
                $this->changed = true;
            }
        }
    }
    /**
     * Convert array of legacy array property names to new camel case names.
     *
     * @param array $properties Properties
     *
     * @return array
     */
    protected static function toCamel(array $properties): array
    {
        $result = [];
        foreach ($properties as $prop) {
            $result['set'.\ucfirst(Tools::toCamelCase($prop))] = $prop;
        }
        return $result;
    }

    /**
     * Get whether this setting was changed, also applies changes.
     *
     * @return boolean
     */
    public function hasChanged(): bool
    {
        return $this->changed;
    }
    /**
     * Apply changes.
     *
     * @return static
     */
    public function applyChanges(): self
    {
        $this->changed = false;
        return $this;
    }
}
