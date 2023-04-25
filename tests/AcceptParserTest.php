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

        $parser = new AcceptParser();
        $parser->parse($source);
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
     * @dataProvider scoreDataProvider
     *
     * @param MediaType[] $expected
     */
    public function test_calculate_score(string $source, array $expected): void
    {
        $parser = new AcceptParser();
        $objs   = $parser->parse($source);

        $this->assertEquals($expected, $objs);
    }

    public static function scoreDataProvider(): Generator
    {
        yield['*', [new MediaType('*/*', [], 1.0)]];
        yield['*/*;q=0.8', [new MediaType('*/*', ['q' => '0.8'], 10.8)]];
        yield['type/*', [new MediaType('type/*', [], 1001.0)]];
        yield['type/subtype', [new MediaType('type/subtype', [], 1101.0)]];
        yield['type/subtype;version=1.0', [new MediaType('type/subtype', ['version' => '1.0'], 1111.0)]];
    }

    /**
     * @dataProvider sortDataProvider
     *
     * @param MediaType[] $expected
     */
    public function test_sort_result(string $source, array $expected): void
    {
        $parser = new AcceptParser();
        $objs   = $parser->parse($source);

        $this->assertEquals($expected, $objs);
    }

    public function sortDataProvider(): Generator
    {
        yield[
          '*;q=0.8,type/*',
          [
            new MediaType('type/*', [], 1001.0),
            new MediaType('*/*', ['q' => '0.8'], 10.8),
          ],
        ];

        yield[
          'type/subtype;q=0.9, text/css;q=0.8',
          [
            new MediaType('type/subtype', ['q' => '0.9'], 1110.9),
            new MediaType('text/css', ['q' => '0.8'], 1110.8),
          ],
        ];

        // Same score
        yield[
          'type/subtype, text/css',
          [
            new MediaType('type/subtype', [], 1101.0),
            new MediaType('text/css', [], 1101.0),
          ],
        ];
    }

    /**
     * @dataProvider duplicateDataProvider
     *
     * @param MediaType[] $expected
     */
    public function test_duplicate_items(string $source, array $expected): void
    {
        $parser = new AcceptParser();
        $objs   = $parser->parse($source);

        $this->assertEquals($expected, $objs);
    }

    public function duplicateDataProvider(): Generator
    {
        yield[
          '*,*/*',
          [
            new MediaType('*/*', [], 1.0),
          ],
        ];

        yield[
          '*;q=0.5, *',
          [
            new MediaType('*/*', ['q' => '0.5'], 10.5),
            new MediaType('*/*', [], 1.0),
          ],
        ];
    }
}
