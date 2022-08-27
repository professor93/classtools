<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

declare(strict_types=1);

namespace Uzbek\ClassTools\Instantiator;

use Uzbek\ClassTools\Exception\LogicException;

/**
 * Instantiate reflected class
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
final class Instantiator
{
    private ?\ReflectionClass $class = null;

    /**
     * Set class to instantiate
     */
    public function setReflectionClass(\ReflectionClass $reflectionClass): void
    {
        $this->class = $reflectionClass;
    }

    /**
     * Get loaded reflection class
     *
     * @throws LogicException  If reflected class is not loaded
     */
    public function getReflectionClass(): \ReflectionClass
    {
        if (!isset($this->class)) {
            throw new LogicException("Reflected class not loaded");
        }

        return $this->class;
    }

    /**
     * Get number of required constructor parameters
     */
    public function countConstructorArgs(): int
    {
        if (($reflectionMethod = $this->class->getConstructor()) !== null) {
            return $reflectionMethod->getNumberOfRequiredParameters();
        }

        return 0;
    }

    /**
     * Check if class is instantiable
     */
    public function isInstantiable(): bool
    {
        return $this->getReflectionClass()->isInstantiable();
    }

    /**
     * Check if class is instantiable without constructor parameters
     */
    public function isInstantiableWithoutArgs(): bool
    {
        if (!$this->isInstantiable()) {
            return false;
        }

        return !$this->countConstructorArgs();
    }

    /**
     * Create instance
     *
     * @param  array          $args Optional constructor arguments
     * @return mixed          Instance of reflected class
     * @throws LogicException If reflected class is not instantiable
     */
    public function instantiate(array $args = []): object
    {
        if (!$this->isInstantiable()) {
            throw new LogicException("Reflected class is not instantiable");
        }

        if (count($args) < $this->countConstructorArgs()) {
            throw new LogicException("Unable to instantiate, too few constructor arguments");
        }

        return $this->getReflectionClass()->newInstanceArgs($args);
    }
}
