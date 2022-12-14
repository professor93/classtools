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
 * Cache bound iterator
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
final class CacheFilter extends ClassIterator implements Filter
{
    use FilterTrait;

    private ?\ArrayIterator $cache = null;

    public function getIterator(): \Traversable
    {
        if (!isset($this->cache)) {
            $this->cache = new \ArrayIterator(iterator_to_array($this->getBoundIterator()));
        }

        return $this->cache;
    }
}
