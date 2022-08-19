<?php

declare(strict_types = 1);

namespace Uzbek\ClassTools\Loader;

use Uzbek\ClassTools\Tests\MockSplFileInfo;

class ClassLoaderTest extends \PHPUnit\Framework\TestCase
{
    public function testClassLoader()
    {
        $iterator = $this->getMockBuilder('Uzbek\ClassTools\Iterator\ClassIterator')
            ->disableOriginalConstructor()
            ->getMock();

        $iterator->expects($this->once())
            ->method('getClassMap')
            ->will($this->returnValue([
                'UnloadedClass' => new MockSplFileInfo(
                    '<?php class UnloadedClass { function foo(){return "bar";} }'
                )
            ]));

        $loader = new ClassLoader($iterator, true);

        $unloadedClass = new \UnloadedClass;
        $this->assertSame('bar', $unloadedClass->foo());

        $loader->unregister();
    }
}
