<?php declare(strict_types=1);

namespace HttpAccept\Tests\Utility;

use Generator;
use HttpAccept\Utility\MimeValidator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class MimeValidatorTest extends TestCase
{
    /**
     * @dataProvider invalidDataProvider
     */
    public function test_invalid_mime_format(string $source): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid media-type format');

        (new MimeValidator())->validate($source);
    }

    public function invalidDataProvider(): Generator
    {
        yield['   '];
        yield['   /subtype'];
        yield['type'];
        yield['type/   '];
        yield['type/subtype/error'];
    }

    /**
     * @dataProvider asteriskDataProvider
     */
    public function test_validate_asterisk(string $source): void
    {
        $name = (new MimeValidator())->validate($source);
        $this->assertSame('*/*', $name);
    }

    public function asteriskDataProvider(): Generator
    {
        yield['*'];
        yield['  *  '];
    }
}
