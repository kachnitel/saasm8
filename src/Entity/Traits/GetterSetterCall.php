<?php

namespace App\Entity\Traits;

use Doctrine\Common\Collections\Collection;

trait GetterSetterCall
{
    // @phpstan-ignore missingType.iterableValue
    public function __call(string $name, array $arguments): mixed
    {
        if (preg_match('/^get(.+)$/', $name, $matches)) {
            $property = lcfirst($matches[1]);
            if (property_exists($this, $property)) {
                return $this->$property;
            }
        }

        if (preg_match('/^set(.+)$/', $name, $matches)) {
            $property = lcfirst($matches[1]);

            if ($property === 'id') {
                throw new \Error("Cannot set property $property");
            }

            // ensure property isn't a collection - we only want add/remove methods to be called
            if (property_exists($this, $property) && !($this->$property instanceof Collection)) {
                $this->$property = $arguments[0];
                return $this;
            }
        }

        if (preg_match('/^add(.+)$/', $name, $matches)) {
            $property = $this->pluralize(lcfirst($matches[1]));
            if (property_exists($this, $property) && $this->$property instanceof Collection) {
                $this->$property->add($arguments[0]);

                try {
                    $method = 'set' . $this->getClass();
                    $arguments[0]->$method($this);
                } catch (\Error $e) {}

                return $this;
            }
        }

        if (preg_match('/^remove(.+)$/', $name, $matches)) {
            $property = $this->pluralize(lcfirst($matches[1]));
            if (property_exists($this, $property) && $this->$property instanceof Collection) {
                $this->$property->removeElement($arguments[0]);

                try {
                    $method = 'set' . $this->getClass();
                    $arguments[0]->$method(null);
                } catch (\Error $e) {}

                return $this;
            }
        }

        throw new \BadMethodCallException("Method $name does not exist");
    }

    public function __get(string $name): mixed
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }

        throw new \Error("Property $name does not exist");
    }

    private function pluralize(string $singular): string
    {
        if (substr($singular, -1) === 'y') {
            return substr($singular, 0, -1) . 'ies';
        }

        return $singular . 's';
    }

    private function getClass(): string
    {
        return (new \ReflectionClass($this))->getShortName();
    }
}
