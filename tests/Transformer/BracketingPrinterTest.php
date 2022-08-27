<?php

declare(strict_types=1);

namespace Uzbek\ClassTools\Transformer;

use PhpParser\ParserFactory;

final class BracketingPrinterTest extends \PHPUnit\Framework\TestCase
{
    public function testPrintWithBrackets(): void
    {
        $parserFactory = new ParserFactory();
        $parser = $parserFactory->create(ParserFactory::PREFER_PHP5);
        $bracketingPrinter = new BracketingPrinter();

        $stmts = $parser->parse(
            <<<EOF
<?php namespace foo;
class Bar
{
}
EOF
        );

        $expected =
            <<<EOF
namespace foo {
    class Bar
    {
    }
}
EOF;

        $this->assertSame($expected, $bracketingPrinter->prettyPrint($stmts));
    }
}
