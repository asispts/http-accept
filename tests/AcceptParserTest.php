<?php declare(strict_types=1);

namespace HttpAccept\Tests;

use Generator;
use HttpAccept\AcceptParser;
use HttpAccept\Data\MediaType;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Throwable;

final class AcceptParserTest extends TestCase
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

        (new AcceptParser())->parse($source);
    }

    public static function invalidDataProvider(): Generator
    {
        yield['', InvalidArgumentException::class, 'Media name is empty'];

        yield['type', InvalidArgumentException::class, 'Invalid media-type format'];
        yield['type/   ', InvalidArgumentException::class, 'Invalid media-type format'];
        yield[' /subtype', InvalidArgumentException::class, 'Invalid media-type format'];
        yield['type/subtype/error', InvalidArgumentException::class, 'Invalid media-type format'];
    }

    /**
     * @dataProvider validDataProvider
     *
     * @param MediaType[] $expected
     */
    public function test_valid_data(string $source, array $expected): void
    {
        $objs = (new AcceptParser())->parse($source);
        $this->assertEquals($expected, $objs);
    }

    public static function validDataProvider(): Generator
    {
        yield['*;q=1.0, */*', [new MediaType('*/*', [], 1.0)]];

        yield[
          'text/html;q=0,*/*,type/*,type/subtype,text/css;q=0.8',
          [
            new MediaType('type/subtype', [], 1100.0),
            new MediaType('type/*', [], 1000.0),
            new MediaType('text/css', ['q' => '0.8'], 880.0),
            new MediaType('*/*', [], 1.0),
            new MediaType('text/html', ['q' => '0'], 0.0),
          ],
        ];
    }
}
