<?php

declare(strict_types=1);

namespace Uzbek\ClassTools\Transformer;

final class WriterTest extends \PHPUnit\Framework\TestCase
{
    public function testApplyTranslation(): void
    {
        $translation = $this->prophesize(\PhpParser\NodeVisitor::class)->reveal();

        $traverser = $this->prophesize(\PhpParser\NodeTraverser::class);
        $traverser->addVisitor($translation)->shouldBeCalled();

        $writer = new Writer($traverser->reveal());
        $writer->apply($translation);
    }

    public function testWrite(): void
    {
        $writer = new Writer();
        $this->assertSame('', $writer->write([]));
    }

    public function testPhpParserException(): void
    {
        $traverser = $this->prophesize(\PhpParser\NodeTraverser::class);
        $traverser->traverse([])->willThrow(new \PhpParser\Error('error'));

        $writer = new Writer($traverser->reveal());

        $this->expectException(\Uzbek\ClassTools\Exception\RuntimeException::class);
        $writer->write([]);
    }
}
