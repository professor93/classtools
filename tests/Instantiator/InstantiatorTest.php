<?php

declare(strict_types=1);

namespace Uzbek\ClassTools\Instantiator;

final class InstantiatorTest extends \PHPUnit\Framework\TestCase
{
    public function testExceptionWhenReflectionClassNotSet(): void
    {
        $instantiator = new Instantiator();
        $this->expectException(\Uzbek\ClassTools\Exception\LogicException::class);
        $instantiator->getReflectionClass();
    }

    public function testIsInstantiable(): void
    {
        $class = $this->getMockBuilder(\ReflectionClass::class)
            ->setConstructorArgs(['\Exception'])
            ->getMock();

        $class->expects($this->atLeastOnce())
            ->method('isInstantiable')
            ->will($this->returnValue(true));

        $constructor = $this->getMockBuilder(\ReflectionMethod::class)
            ->setConstructorArgs(['\Exception', '__construct'])
            ->getMock();

        $constructor->expects($this->atLeastOnce())
            ->method('getNumberOfRequiredParameters')
            ->will($this->returnValue(1));

        $class->expects($this->atLeastOnce())
            ->method('getConstructor')
            ->will($this->returnValue($constructor));

        $instantiator = new Instantiator();
        $instantiator->setReflectionClass($class);

        $this->assertTrue($instantiator->isInstantiable());
        $this->assertFalse($instantiator->isInstantiableWithoutArgs());

        $this->expectException(\Uzbek\ClassTools\Exception\LogicException::class);
        $instantiator->instantiate();
    }

    public function testExceptionWhenInstantiatingNotInstatiable(): void
    {
        $class = $this->getMockBuilder(\ReflectionClass::class)
            ->setConstructorArgs(['\Exception'])
            ->getMock();

        $class->expects($this->atLeastOnce())
            ->method('isInstantiable')
            ->will($this->returnValue(false));

        $instantiator = new Instantiator();
        $instantiator->setReflectionClass($class);

        $this->assertFalse($instantiator->isInstantiable());
        $this->expectException(\Uzbek\ClassTools\Exception\LogicException::class);
        $instantiator->instantiate();
    }

    public function testInstantiate(): void
    {
        $instantiator = new Instantiator();
        $instantiator->setReflectionClass(new \ReflectionClass(\Uzbek\ClassTools\Instantiator\Instantiator::class));
        $this->assertInstanceOf(\Uzbek\ClassTools\Instantiator\Instantiator::class, $instantiator->instantiate());
    }
}
