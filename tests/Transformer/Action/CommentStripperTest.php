<?php

declare(strict_types=1);

namespace Uzbek\ClassTools\Transformer\Action;

use Uzbek\ClassTools\Transformer\Reader;
use Uzbek\ClassTools\Transformer\Writer;

final class CommentStripperTest extends \PHPUnit\Framework\TestCase
{
    public function testStripComments(): void
    {
        $reader = new Reader(
            <<<EOF
<?php
/**
 * File docblock comment
 */

/**
 * Class docblock
 */
class ClassName
{
    /**
     * @var string Some desc
     */
    private \$var;

    /**
     * Some docblock here too
     */
    public function test()
    {
        // inline comment
        return true; // comment at end of line
        /*
            Comment
        */
        # Comment...
    }
}
EOF
        );

        $expected =
<<<EOF
namespace {
    class ClassName
    {
        private \$var;
        public function test()
        {
            return true;
            
        }
    }
}
EOF;

        $writer = new Writer();
        $writer->apply(new CommentStripper());
        $this->assertSame(
            $expected,
            $writer->write($reader->read('ClassName'))
        );
    }
}
