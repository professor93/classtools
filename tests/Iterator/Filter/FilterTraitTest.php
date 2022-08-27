<?php

declare(strict_types=1);

namespace Uzbek\ClassTools\Iterator\Filter;

final class FilterTraitTest extends \PHPUnit\Framework\TestCase
{
    public function testFilterNotBoundException(): void
    {
        $filter = new TypeFilter('');
        $this->expectException(\Uzbek\ClassTools\Exception\LogicException::class);
        $filter->getBoundIterator();
    }
}
