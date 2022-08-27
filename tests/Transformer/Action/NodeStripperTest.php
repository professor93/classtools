<?php

declare(strict_types=1);

namespace Uzbek\ClassTools\Transformer\Action;

use Uzbek\ClassTools\Transformer\Reader;
use Uzbek\ClassTools\Transformer\Writer;

final class NodeStripperTest extends \PHPUnit\Framework\TestCase
{
    public function testStripNodes(): void
    {
        $reader = new Reader(
            <<<EOF
<?php
namespace {
    class ClassName
    {
        public function foobar()
        {
            include "somefile.php";
            echo 'foobar';
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
            echo 'foobar';
        }
    }
}
EOF;

        $writer = new Writer();
        $writer->apply(new NodeStripper('Stmt_Expression'));
        $this->assertSame(
            $expected,
            $writer->write($reader->read('ClassName'))
        );
    }
}
