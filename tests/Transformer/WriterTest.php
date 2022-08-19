<?php

declare(strict_types = 1);

namespace Uzbek\ClassTools\Transformer;

class WriterTest extends \PHPUnit\Framework\TestCase
{
    public function testApplyTranslation()
    {
        $translation = $this->prophesize('PhpParser\NodeVisitor')->reveal();

        $traverser = $this->prophesize('PhpParser\NodeTraverser');
        $traverser->addVisitor($translation)->shouldBeCalled();

        $writer = new Writer($traverser->reveal());
        $writer->apply($translation);
    }

    public function testWrite()
    {
        $writer = new Writer();
        $this->assertSame('', $writer->write([]));
    }

    public function testPhpParserException()
    {
        $traverser = $this->prophesize('PhpParser\NodeTraverser');
        $traverser->traverse([])->willThrow(new \PhpParser\Error('error'));

        $writer = new Writer($traverser->reveal());

        $this->expectException('Uzbek\ClassTools\Exception\RuntimeException');
        $writer->write([]);
    }
}
