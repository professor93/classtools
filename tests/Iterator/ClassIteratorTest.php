<?php

declare(strict_types=1);

namespace Uzbek\ClassTools\Iterator;

use Uzbek\ClassTools\Tests\MockSplFileInfo;
use Uzbek\ClassTools\Tests\MockFinder;

final class ClassIteratorTest extends \PHPUnit\Framework\TestCase
{
    private static \Uzbek\ClassTools\Iterator\ClassIterator $sut;

    public static function setupBeforeClass(): void
    {
        MockFinder::setIterator(
            new \ArrayIterator([
                new MockSplFileInfo('<?php namespace foobar; use \\some\\name; class A {}'),
                new MockSplFileInfo('<?php interface Baz {}'),
                new MockSplFileInfo('<?php class B implements Baz {} class C extends \foobar\A implements Baz {}'),
                new MockSplFileInfo('<?php funct error(){}')
            ])
        );

        self::$sut = new ClassIterator(new MockFinder());
        self::$sut->enableAutoloading();
    }

    public function getSystemUnderTest(): \Uzbek\ClassTools\Iterator\ClassIterator
    {
        return self::$sut;
    }

    public function testGetErrors(): void
    {
        $this->assertCount(1, $this->getSystemUnderTest()->getErrors());
    }

    public function testGetClassmap(): void
    {
        $this->assertArrayHasKey(
            'B',
            $this->getSystemUnderTest()->getClassMap()
        );

        $this->assertInstanceOf(
            \SplFileInfo::class,
            $this->getSystemUnderTest()->getClassMap()['B'],
            'getClassMap should map classnames to SplFileInfo objects'
        );
    }

    public function testExceptionWhenIteratingOverUnloadedClasses(): void
    {
        $stub = $this->getMockBuilder(\Uzbek\ClassTools\Iterator\ClassIterator::class)
            ->disableOriginalConstructor()
            ->setMethods(['getClassMap'])
            ->getMock();

        $stub->expects($this->once())
            ->method('getClassMap')
            ->will($this->returnValue(['ClassThatDoesNotExist' => null]));

        $this->expectException(\Uzbek\ClassTools\Exception\LogicException::class);
        iterator_to_array($stub);
    }

    public function testGetIterator(): void
    {
        $this->assertArrayHasKey(
            'B',
            iterator_to_array($this->getSystemUnderTest())
        );

        $this->assertInstanceOf(
            \ReflectionClass::class,
            iterator_to_array($this->getSystemUnderTest())['B'],
            'getIterator should map classnames to ReflectionClass objects'
        );
    }

    public function testFilteredClassMap(): void
    {
        $classIterator = $this->getSystemUnderTest();

        $this->assertArrayHasKey(
            'B',
            $classIterator->getClassMap(),
            'B is definied and should be present'
        );

        $this->assertArrayNotHasKey(
            'B',
            $classIterator->where('isInterface')->getClassMap(),
            'B is not an interface and should be filtered'
        );
    }

    public function testTypeFilter(): void
    {
        $classIterator = $this->getSystemUnderTest();

        $result = iterator_to_array(
            $classIterator->type('Baz')
        );

        $this->assertArrayNotHasKey(
            'foobar\\A',
            $result,
            'foobar\\A does not implement Baz'
        );

        $this->assertArrayHasKey(
            'B',
            $result,
            'B does implement Baz'
        );

        $result = iterator_to_array(
            $classIterator
                ->type('Baz')
                ->type('foobar\\A')
        );

        $this->assertArrayNotHasKey(
            'B',
            $result,
            'B does not extend foobar\\A'
        );

        $this->assertArrayHasKey(
            'C',
            $result,
            'C extends all'
        );
    }

    public function testNameFilter(): void
    {
        $classIterator = $this->getSystemUnderTest();

        $result = iterator_to_array(
            $classIterator->name('/A/')
        );

        $this->assertArrayNotHasKey(
            'Baz',
            $result
        );

        $this->assertArrayHasKey(
            'foobar\\A',
            $result
        );

        $result = iterator_to_array(
            $classIterator->name('/B/')->name('/az/')
        );

        $this->assertArrayNotHasKey(
            'foobar\\A',
            $result
        );

        $this->assertArrayHasKey(
            'Baz',
            $result
        );
    }

    public function testNamespaceFilter(): void
    {
        $classIterator = $this->getSystemUnderTest();

        $result = iterator_to_array(
            $classIterator->inNamespace('foobar')
        );

        $this->assertArrayNotHasKey(
            'Baz',
            $result
        );

        $this->assertArrayHasKey(
            'foobar\\A',
            $result
        );
    }

    public function testWhereFilter(): void
    {
        $classIterator = $this->getSystemUnderTest();

        $result = iterator_to_array(
            $classIterator->where('isInterface')
        );

        $this->assertArrayNotHasKey(
            'B',
            $result,
            'B is not an interface'
        );

        $this->assertArrayHasKey(
            'Baz',
            $result,
            'Baz is an interface'
        );
    }

    public function testNotFilter(): void
    {
        $classIterator = $this->getSystemUnderTest();

        $result = iterator_to_array(
            $classIterator->not($classIterator->where('isInterface'))
        );

        $this->assertArrayNotHasKey(
            'Baz',
            $result,
            'Baz is an interface (and thus not included using the not filter)'
        );

        $this->assertArrayHasKey(
            'B',
            $result,
            'B is not an interface (and thus included using the not filter)'
        );
    }

    public function testCacheFilter(): void
    {
        $classIterator = $this->getSystemUnderTest();

        $this->assertNotSame(
            $classIterator->getIterator(),
            $classIterator->getIterator()
        );

        $classIterator = $classIterator->cache();

        $this->assertSame(
            $classIterator->getIterator(),
            $classIterator->getIterator()
        );
    }

    public function testAttributeFilter(): void
    {
        if (version_compare(PHP_VERSION, '8.0.1') >= 0) {
            # define our own system under test, adding attributes etc to standard test will break
            # tests for minimize etc
            MockFinder::setIterator(
                new \ArrayIterator([
                    new MockSplFileInfo(''),
                    new MockSplFileInfo('<?php #[Attribute] class NotUsed {  }'),
                    new MockSplFileInfo('<?php #[Attribute] class CoolStuff {  }'),
                    new MockSplFileInfo('<?php #[Attribute] class EvenCoolerStuff {  }'),
                    new MockSplFileInfo('<?php #[Attribute] class WayCool extends CoolStuff {  }'),
                    new MockSplFileInfo('<?php #[CoolStuff] class BeCool { }'),
                    new MockSplFileInfo("<?php #[EvenCoolerStuff]\n#[CoolStuff]\nclass TheCoolest { }"),
                    new MockSplFileInfo('<?php #[WayCool] class InheritedCoolness {}'),
                    new MockSplFileInfo('<?php class NotCool {}')
                ])
            );

            $classIterator = new ClassIterator(new MockFinder());
            $classIterator->enableAutoloading();

            // look for class tagged with 'CoolStuff'
            // we should find both BeCool, TheCoolest  and WayCool because filter uses instance inheritance
            // by default, thus WayCool extends CoolStuff and is detect by this filter
            $result = iterator_to_array(
                $classIterator->attribute('CoolStuff')
            );
            $this->assertArrayHasKey(
                'BeCool',
                $result
            );
            $this->assertArrayHasKey(
                'TheCoolest',
                $result
            );
            $this->assertArrayHasKey(
                'InheritedCoolness',
                $result
            );
            $this->assertCount(
                3,
                $result
            );

            // look for class tagged with 'EvenCoolerStuff'
            // we should find only TheCoolest
            $result = iterator_to_array(
                $classIterator->attribute('EvenCoolerStuff')
            );
            $this->assertArrayHasKey(
                'TheCoolest',
                $result
            );
            $this->assertCount(
                1,
                $result
            );

            // look for an attribute not tagged onto anything, should return empty
            $result = iterator_to_array(
                $classIterator->attribute('NotUsed')
            );
            $this->assertEmpty(
                $result
            );
        } else {
            $this->markTestSkipped('This test requires PHP 8.0.1 or higher');
        }
    }

    public function testMinimize(): void
    {
        $expected = <<<EOL
<?php namespace foobar {
    class A
    {
    }
}
namespace {
    interface Baz
    {
    }
}
namespace {
    class B implements \\Baz
    {
    }
}
namespace {
    class C extends \\foobar\\A implements \\Baz
    {
    }
}

EOL;

        $this->assertSame(
            $expected,
            $this->getSystemUnderTest()->minimize()
        );
    }
}
