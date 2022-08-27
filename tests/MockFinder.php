<?php

declare(strict_types=1);

namespace Uzbek\ClassTools;

final class MockFinder extends \Symfony\Component\Finder\Finder
{
    private static ?iterable $iterator = null;

    public static function setIterator(\Traversable $traversable): void
    {
        self::$iterator = $traversable;
    }

    public function getIterator(): \Iterator
    {
        return self::$iterator;
    }
}
