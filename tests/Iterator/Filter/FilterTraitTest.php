<?php

declare(strict_types = 1);

namespace Uzbek\ClassTools\Iterator\Filter;

class FilterTraitTest extends \PHPUnit\Framework\TestCase
{
    public function testFilterNotBoundException()
    {
        $filter = new TypeFilter('');
        $this->expectException('Uzbek\ClassTools\Exception\LogicException');
        $filter->getBoundIterator();
    }
}
