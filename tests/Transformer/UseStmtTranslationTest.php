<?php

declare(strict_types=1);

namespace Uzbek\ClassTools\Transformer;

final class UseStmtTranslationTest extends \PHPUnit\Framework\TestCase
{
    public function testSaveNamespacedUseStatements(): void
    {
        $reader = new Reader(
            <<<EOF
<?php
namespace foo {
    use Exception;
    class ClassName
    {
    }
}
EOF
        );

        $expected =
<<<EOF
namespace foo {
    use Exception;
    class ClassName
    {
    }
}
EOF;

        $writer = new Writer();

        $this->assertSame(
            $expected,
            $writer->write($reader->read('foo\ClassName'))
        );
    }

    public function testSaveGlobalUseStatements(): void
    {
        $reader = new Reader(
            <<<EOF
<?php
use random\Exception;
class ClassName
{
}
EOF
        );

        $expected =
<<<EOF
namespace {
    use random\Exception;
    class ClassName
    {
    }
}
EOF;

        $writer = new Writer();

        $this->assertSame(
            $expected,
            $writer->write($reader->read('ClassName'))
        );
    }
}
