<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

declare(strict_types=1);

namespace Uzbek\ClassTools\Iterator;

use Symfony\Component\Finder\Finder;
use Uzbek\ClassTools\Transformer\Writer;
use Uzbek\ClassTools\Transformer\MinimizingWriter;
use Uzbek\ClassTools\Iterator\Filter\CacheFilter;
use Uzbek\ClassTools\Iterator\Filter\NameFilter;
use Uzbek\ClassTools\Iterator\Filter\NamespaceFilter;
use Uzbek\ClassTools\Iterator\Filter\NotFilter;
use Uzbek\ClassTools\Iterator\Filter\TypeFilter;
use Uzbek\ClassTools\Iterator\Filter\WhereFilter;
use Uzbek\ClassTools\Iterator\Filter\AttributeFilter;
use Uzbek\ClassTools\Exception\LogicException;
use Uzbek\ClassTools\Loader\ClassLoader;
use Uzbek\ClassTools\Exception\ReaderException;

/**
 * Iterate over classes found in filesystem
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class ClassIterator implements ClassIteratorInterface
{
    /**
     * @var SplFileInfo[] Maps names to SplFileInfo objects
     */
    private array $classMap = [];

    /**
     * @var string[]
     */
    private array $errors = [];

    private ?\Uzbek\ClassTools\Loader\ClassLoader $loader = null;

    /**
     * Scan filesystem for classes, interfaces and traits
     */
    public function __construct(Finder $finder = null)
    {
        /** @var \Symfony\Component\Finder\SplFileInfo $fileInfo */
        foreach (($finder ?: []) as $fileInfo) {
            $fileInfo = new SplFileInfo($fileInfo);
            try {
                foreach ($fileInfo->getReader()->getDefinitionNames() as $name) {
                    $this->classMap[$name] = $fileInfo;
                }
            } catch (ReaderException $readerException) {
                $this->errors[] = $readerException->getMessage();
            }
        }
    }

    /**
     * Enable garbage collection of the autoloader at destruct
     */
    public function __destruct()
    {
        $this->disableAutoloading();
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getClassMap(): array
    {
        return $this->classMap;
    }

    public function enableAutoloading(): void
    {
        $this->loader = new ClassLoader($this, true);
    }

    public function disableAutoloading(): void
    {
        if (isset($this->loader)) {
            $this->loader->unregister();
            unset($this->loader);
        }
    }

    public function getIterator(): \Traversable
    {
        foreach (array_keys($this->classMap) as $name) {
            try {
                yield $name => new \ReflectionClass($name);
            } catch (\ReflectionException $reflectionException) {
                $msg = sprintf('Unable to iterate, %s, is autoloading enabled?', $reflectionException->getMessage());
                throw new LogicException($msg, 0, $reflectionException);
            }
        }
    }

    public function filter(Filter $filter): Filter
    {
        $filter->bindTo($this);
        return $filter;
    }

    public function type(string $typename): Filter
    {
        return $this->filter(new TypeFilter($typename));
    }

    public function name(string $pattern): Filter
    {
        return $this->filter(new NameFilter($pattern));
    }

    public function inNamespace(string $namespace): Filter
    {
        return $this->filter(new NamespaceFilter($namespace));
    }

    public function where(string $methodName, $expectedReturn = true): Filter
    {
        return $this->filter(new WhereFilter($methodName, $expectedReturn));
    }

    public function not(Filter $filter): Filter
    {
        return $this->filter(new NotFilter($filter));
    }

    public function cache(): Filter
    {
        return $this->filter(new CacheFilter());
    }

    public function attribute(string $attribute_class_name): Filter
    {
        return $this->filter(new AttributeFilter($attribute_class_name));
    }

    public function transform(Writer $writer): string
    {
        $code = '';

        /** @var SplFileInfo $fileInfo */
        foreach ($this->classMap as $name => $fileInfo) {
            $code .= $writer->write($fileInfo->getReader()->read($name)) . "\n";
        }

        return sprintf('<?php %s', $code);
    }

    public function minimize(): string
    {
        return $this->transform(new MinimizingWriter());
    }
}
