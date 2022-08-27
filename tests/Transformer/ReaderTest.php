<?php

declare(strict_types=1);

namespace Uzbek\ClassTools\Transformer;

final class ReaderTest extends \PHPUnit\Framework\TestCase
{
    public function testFindDefinitions(): void
    {
        $reader = new Reader(
            <<<EOF
<?php
namespace foo;
class ClassName {}
interface InterfaceName {}
trait TraitName {}
EOF
        );

        $this->assertEquals(
            [
                'foo\\ClassName',
                'foo\\InterfaceName',
                'foo\\TraitName'
            ],
            $reader->getDefinitionNames()
        );
    }

    public function testHasDefinition(): void
    {
        $reader = new Reader("<?php class ClassName {}");
        $this->assertTrue($reader->hasDefinition('ClassName'));
        $this->assertTrue($reader->hasDefinition('\\ClassName'));

        $reader = new Reader("<?php namespace foo; class ClassName {}");
        $this->assertTrue($reader->hasDefinition('foo\\ClassName'));
        $this->assertTrue($reader->hasDefinition('\\foo\\ClassName'));
    }

    public function testFindBracketedDefinitions(): void
    {
        $reader = new Reader(
            <<<EOF
<?php
namespace foo {
    class ClassName {}
    class AnotherClassName {}
}
namespace bar {
    interface InterfaceName {}
}
namespace {
    trait TraitName {}
}
EOF
        );

        $this->assertEquals(
            [
                'foo\\ClassName',
                'foo\\AnotherClassName',
                'bar\\InterfaceName',
                'TraitName'
            ],
            $reader->getDefinitionNames()
        );
    }

    public function testFindGlobalDefinitions(): void
    {
        $reader = new Reader(
            <<<EOF
<?php
class ClassName {}
interface InterfaceName {}
EOF
        );

        $this->assertEquals(
            [
                'ClassName',
                'InterfaceName'
            ],
            $reader->getDefinitionNames()
        );
    }

    public function testReadUndefinedClass(): void
    {
        $reader = new Reader('');
        $this->expectException(\Uzbek\ClassTools\Exception\RuntimeException::class);
        $reader->read('UndefinedClass');
    }

    public function testRead(): void
    {
        $reader = new Reader('<?php class FooBar {}');
        $this->assertIsArray(
            $reader->read('FooBar')
        );
    }

    public function testReadAll(): void
    {
        $reader = new Reader('');
        $this->assertIsArray(
            $reader->readAll()
        );
    }

    public function testSyntaxError(): void
    {
        $this->expectException(\Uzbek\ClassTools\Exception\ReaderException::class);
        new Reader('<?php functi hej(){}');
    }
}
