# uzbek/classtools

[![Packagist Version](https://img.shields.io/packagist/v/uzbek/classtools.svg?style=flat-square)](https://packagist.org/packages/uzbek/classtools)
[![Build Status](https://img.shields.io/travis/uzbek/classtools/master.svg?style=flat-square)](https://travis-ci.org/uzbek/classtools)
[![Quality Score](https://img.shields.io/scrutinizer/g/uzbek/classtools.svg?style=flat-square)](https://scrutinizer-ci.com/g/uzbek/classtools)

---

## Forked from https://github.com/hanneskod/classtools

---

Find, extract and process classes from the file system.

Installation
------------
Install using **[composer](http://getcomposer.org/)**. Exists as
**[uzbek/classtools](https://packagist.org/packages/uzbek/classtools)**
in the **[packagist](https://packagist.org/)** repository. From the command line
use:

    composer require uzbek/classtools

Using the iterator
------------------
[ClassIterator](src/Iterator/ClassIterator.php) consumes a [symfony
finder](http://symfony.com/doc/current/components/finder.html) and scans files
for php classes, interfaces and traits.

### Access the class map

`getClassMap()` returns a map of class names to
[SplFileInfo](http://api.symfony.com/2.5/Symfony/Component/Finder/SplFileInfo.html)
objects.

<!--
    @example getClassMap()
    @expectOutput "/uzbek/"
-->
```php
$finder = new Symfony\Component\Finder\Finder;
$iter = new Uzbek\ClassTools\Iterator\ClassIterator($finder->in('src'));

// Print the file names of classes, interfaces and traits in 'src'
foreach ($iter->getClassMap() as $classname => $splFileInfo) {
    echo $classname.': '.$splFileInfo->getRealPath();
}
```

### Find syntax errors

Source files containing syntax errors can not be parsed and hence no information
on contained classes can be retrieved. Use `getErrors()` to read the list of
encountered errors.

<!--
    @example getErrors()
    @expectOutput "/Array/"
-->
```php
$finder = new Symfony\Component\Finder\Finder;
$iter = new Uzbek\ClassTools\Iterator\ClassIterator($finder->in('src'));

print_r($iter->getErrors());
```

### Iterate over ReflectionClass objects

ClassIterator is also a
[Traversable](http://php.net/manual/en/class.traversable.php), that on iteration
yields class names as keys and
[ReflectionClass](http://php.net/manual/en/class.reflectionclass.php) objects as
values.

Note that to use reflection the classes found in filesystem must be
included in the environment. Enable autoloading to dynamically load classes from
a ClassIterator.

<!--
    @example enableAutoloading()
    @expectOutput "/uzbek/"
-->
```php
$finder = new Symfony\Component\Finder\Finder();
$iter = new Uzbek\ClassTools\Iterator\ClassIterator($finder->in('src'));

// Enable reflection by autoloading found classes
$iter->enableAutoloading();

// Print all classes, interfaces and traits in 'src'
foreach ($iter as $class) {
    echo $class->getName();
}
```

### Filter based on class properties

[ClassIterator](src/Iterator/ClassIterator.php) is filterable and filters are
chainable.

<!--
    @example filter
    @expectOutput "/uzbek/"
-->
```php
$finder = new Symfony\Component\Finder\Finder();
$iter = new Uzbek\ClassTools\Iterator\ClassIterator($finder->in('src'));
$iter->enableAutoloading();

// Print all Filter types (including the interface itself)
foreach ($iter->type('uzbek\ClassTools\Iterator\Filter') as $class) {
    echo $class->getName();
}

// Print definitions in the Iterator namespace whose name contains 'Class'
foreach ($iter->inNamespace('uzbek\ClassTools\Iterator\Filter')->name('/type/i') as $class) {
    echo $class->getName();
}

// Print implementations of the Filter interface
foreach ($iter->type('uzbek\ClassTools\Iterator\Filter')->where('isInstantiable') as $class) {
    echo $class->getName();
}
```

### Negate filters

Filters can also be negated by wrapping them in `not()` method calls.

<!--
    @example negation
    @expectOutput "/uzbek/"
-->
```php
$finder = new Symfony\Component\Finder\Finder();
$iter = new Uzbek\ClassTools\Iterator\ClassIterator($finder->in('src'));
$iter->enableAutoloading();

// Print all classes, interfaces and traits NOT instantiable
foreach ($iter->not($iter->where('isInstantiable')) as $class) {
    echo $class->getName();
}
```

### Transforming classes

Found class, interface and trait definitions can be transformed and written to a
single file.

<!--
    @example transformation
    @expectOutput "/\<\?php/"
-->
```php
$finder = new Symfony\Component\Finder\Finder();
$iter = new Uzbek\ClassTools\Iterator\ClassIterator($finder->in('src'));
$iter->enableAutoloading();

// Print all found definitions in one snippet
echo $iter->minimize();

// The same can be done using
echo $iter->transform(new Uzbek\ClassTools\Transformer\MinimizingWriter);
```

Using the transformer
---------------------

### Wrap code in namespace

<!-- @ignore -->
```php
$reader = new Reader("<?php class Bar {}");
$writer = new Writer;
$writer->apply(new Action\NamespaceWrapper('Foo'));

// Outputs class Bar wrapped in namespace Foo
echo $writer->write($reader->read('Bar'));
```
