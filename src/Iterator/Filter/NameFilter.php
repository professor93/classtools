<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

declare(strict_types=1);

namespace Uzbek\ClassTools\Iterator\Filter;

use Uzbek\ClassTools\Iterator\ClassIterator;
use Uzbek\ClassTools\Iterator\Filter;

/**
 * Filter classes based on name
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
final class NameFilter extends ClassIterator implements Filter
{
    use FilterTrait;

    /**
     * Register matching regular expression
     */
    public function __construct(/**
     * @var string Regular expression for matching definition names
     */
    private readonly string $pattern
    ) {
        parent::__construct();
    }

    public function getIterator(): \Traversable
    {
        foreach ($this->getBoundIterator() as $className => $reflectedClass) {
            if (preg_match($this->pattern, (string) $className)) {
                yield $className => $reflectedClass;
            }
        }
    }
}
