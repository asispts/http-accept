<?php declare(strict_types=1);

namespace HttpAccept\Tests;

use Generator;
use HttpAccept\ContentTypeParser;
use HttpAccept\Data\MediaType;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Throwable;

final class ContentTypeParserTest extends TestCase
{
    /**
     * @dataProvider invalidDataProvider
     *
     * @param class-string<Throwable> $exception
     */
    public function test_parse_invalid_data(string $source, string $exception, string $message): void
    {
        $this->expectException($exception);
        $this->expectExceptionMessage($message);

        $parser = new ContentTypeParser();
        $parser->parse($source);
    }

    public static function invalidDataProvider(): Generator
    {
        yield['', InvalidArgumentException::class, 'Media name is empty'];

        yield['type', InvalidArgumentException::class, 'Invalid media-type format'];
        yield['type/   ', InvalidArgumentException::class, 'Invalid media-type format'];
        yield[' /subtype', InvalidArgumentException::class, 'Invalid media-type format'];
        yield['type/subtype/error', InvalidArgumentException::class, 'Invalid media-type format'];

        yield['type/subtype, type/*', InvalidArgumentException::class, 'Invalid Content-Type format'];
    }

    /**
     * @dataProvider validDataProvider
     */
    public function test_parse_valid_data(string $source, MediaType $expected): void
    {
        $parser = new ContentTypeParser();
        $actual = $parser->parse($source);

        $this->assertEquals($expected, $actual);
    }

    public static function validDataProvider(): Generator
    {
        yield['*', new MediaType('*/*', [])];
        yield[
          'application/xml; version=1.0; encoding=utf-8',
          new MediaType('application/xml', ['version' => '1.0', 'encoding' => 'utf-8']),
        ];
    }
}
