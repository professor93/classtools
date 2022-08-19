<?php

declare(strict_types = 1);

namespace Uzbek\ClassTools\Instantiator;

class InstantiatorTest extends \PHPUnit\Framework\TestCase
{
    public function testExceptionWhenReflectionClassNotSet()
    {
        $in = new Instantiator;
        $this->expectException('Uzbek\ClassTools\Exception\LogicException');
        $in->getReflectionClass();
    }

    public function testIsInstantiable()
    {
        $class = $this->getMockBuilder('\ReflectionClass')
            ->setConstructorArgs(['\Exception'])
            ->getMock();

        $class->expects($this->atLeastOnce())
            ->method('isInstantiable')
            ->will($this->returnValue(true));

        $constructor = $this->getMockBuilder('\ReflectionMethod')
            ->setConstructorArgs(['\Exception', '__construct'])
            ->getMock();

        $constructor->expects($this->atLeastOnce())
            ->method('getNumberOfRequiredParameters')
            ->will($this->returnValue(1));

        $class->expects($this->atLeastOnce())
            ->method('getConstructor')
            ->will($this->returnValue($constructor));

        $in = new Instantiator;
        $in->setReflectionClass($class);

        $this->assertTrue($in->isInstantiable());
        $this->assertFalse($in->isInstantiableWithoutArgs());

        $this->expectException('Uzbek\ClassTools\Exception\LogicException');
        $in->instantiate();
    }

    public function testExceptionWhenInstantiatingNotInstatiable()
    {
        $class = $this->getMockBuilder('\ReflectionClass')
            ->setConstructorArgs(['\Exception'])
            ->getMock();

        $class->expects($this->atLeastOnce())
            ->method('isInstantiable')
            ->will($this->returnValue(false));

        $in = new Instantiator;
        $in->setReflectionClass($class);

        $this->assertFalse($in->isInstantiable());
        $this->expectException('Uzbek\ClassTools\Exception\LogicException');
        $in->instantiate();
    }

    public function testInstantiate()
    {
        $in = new Instantiator;
        $in->setReflectionClass(new \ReflectionClass('Uzbek\ClassTools\Instantiator\Instantiator'));
        $this->assertInstanceOf('Uzbek\ClassTools\Instantiator\Instantiator', $in->instantiate());
    }
}
