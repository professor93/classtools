<?php

declare(strict_types=1);

namespace Uzbek\ClassTools\Transformer\Action;

use Uzbek\ClassTools\Transformer\Reader;
use Uzbek\ClassTools\Transformer\Writer;

final class NamespaceCrawlerTest extends \PHPUnit\Framework\TestCase
{
    public function testCrawlNamespaces(): void
    {
        $reader = new Reader(
            <<<EOF
<?php
namespace {
    class ClassName
    {
        public function foobar()
        {
            new NamespaceCrawlerTest();
            new \Uzbek\ClassTools\Transformer\Action\NamespaceCrawlerTest();
        }
    }
}
EOF
        );

        $expected =
<<<EOF
namespace {
    class ClassName
    {
        public function foobar()
        {
            new \Uzbek\ClassTools\Transformer\Action\NamespaceCrawlerTest();
            new \Uzbek\ClassTools\Transformer\Action\NamespaceCrawlerTest();
        }
    }
}
EOF;

        $writer = new Writer();
        $writer->apply(new NameResolver());
        $writer->apply(new NamespaceCrawler(['\Uzbek\ClassTools\Transformer\Action']));
        $this->assertSame(
            $expected,
            $writer->write($reader->read('ClassName'))
        );
    }

    public function testCrawlUnableToResolveNamespaceException(): void
    {
        $reader = new Reader(
            <<<EOF
<?php
class ClassName
{
    public function foobar()
    {
        new NonExistingClass();
    }
}
EOF
        );

        $writer = new Writer();
        $writer->apply(new NameResolver());
        $writer->apply(new NamespaceCrawler(['']));

        // NonExistingClass does not resolve
        $this->expectException(\Uzbek\ClassTools\Exception\RuntimeException::class);
        $writer->write($reader->read('ClassName'));
    }

    public function testWhitelistNamespace(): void
    {
        $reader = new Reader(
            <<<EOF
<?php
class ClassName
{
    public function foobar()
    {
        new \whitelist\NonExistingClass();
    }
}
EOF
        );

        $writer = new Writer();
        $writer->apply(new NameResolver());
        $writer->apply(new NamespaceCrawler([''], ['whitelist']));

        // NonExistingClass does not resolve, but no exception is thrown
        $this->assertIsString($writer->write($reader->read('ClassName')));
    }
}
