<?php declare(strict_types=1);

namespace HttpAccept\Tests\Data;

use HttpAccept\Utility\Parser;
use PHPUnit\Framework\TestCase;

final class MediaTypeTest extends TestCase
{
    public function test_properties(): void
    {
        $types = (new Parser())->parse('*;q=1.0;version=1');

        $this->assertSame('*', $types[0]->name());
        $this->assertTrue($types[0]->hasParamater('q'));
        $this->assertSame('1.0', $types[0]->getParameter('q'));
        $this->assertSame(['q' => '1.0', 'version' => '1'], $types[0]->parameters());
    }

    public function test_parameter_case_insensitive(): void
    {
        $types = (new Parser())->parse('*;q=1.0;version=1');

        $this->assertTrue($types[0]->hasParamater('Q'));
    }
}
