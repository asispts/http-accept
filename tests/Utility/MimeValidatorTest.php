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

    public static function invalidDataProvider(): Generator
    {
        yield['   '];
        yield['   /subtype'];
        yield['type'];
        yield['type/   '];
        yield['type/subtype/error'];
    }

    /**
     * @dataProvider nameDataProvider
     */
    public function test_validate_name(string $source, string $expected): void
    {
        $name = (new MimeValidator())->validate($source);
        $this->assertSame($expected, $name);
    }

    public static function nameDataProvider(): Generator
    {
        yield['  *  ', '*/*'];
        yield['   type   /  subtype', 'type/subtype'];
    }
}
