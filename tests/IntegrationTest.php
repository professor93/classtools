<?php

declare(strict_types=1);

namespace Uzbek\ClassTools;

final class IntegrationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Should not trigger an error, se issue #10
     */
    public function testNullableTypes(): void
    {
        $this->assertTrue(
            (bool) new Transformer\Reader('<?php function someMethod(string $some_param) : ?string {return null;}')
        );
    }
}
