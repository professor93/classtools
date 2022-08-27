<?php

declare(strict_types=1);

namespace Uzbek\ClassTools\Loader;

use Uzbek\ClassTools\Tests\MockSplFileInfo;

final class ClassLoaderTest extends \PHPUnit\Framework\TestCase
{
    public function testClassLoader(): void
    {
        $iterator = $this->getMockBuilder(\Uzbek\ClassTools\Iterator\ClassIterator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $iterator->expects($this->once())
            ->method('getClassMap')
            ->will($this->returnValue([
                'UnloadedClass' => new MockSplFileInfo(
                    '<?php class UnloadedClass { function foo(){return "bar";} }'
                )
            ]));

        $classLoader = new ClassLoader($iterator, true);

        $unloadedClass = new \UnloadedClass();
        $this->assertSame('bar', $unloadedClass->foo());

        $classLoader->unregister();
    }
}
