<?php declare(strict_types=1);

namespace HttpAccept\Tests;

use Generator;
use HttpAccept\AcceptEncodingParser;
use HttpAccept\Data\MediaType;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Throwable;

final class AcceptEncodingParserTest extends TestCase
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

        (new AcceptEncodingParser())->parse($source);
    }

    public static function invalidDataProvider(): Generator
    {
        yield['', InvalidArgumentException::class, 'Media name is empty'];
    }

    /**
     * @dataProvider validDataProvider
     *
     * @param MediaType[] $expected
     */
    public function test_valid_data(string $source, array $expected): void
    {
        $objs = (new AcceptEncodingParser())->parse($source);
        $this->assertEquals($expected, $objs);
    }

    public static function validDataProvider(): Generator
    {
        yield[
          '*;q=0, identity;q=0.5, gzip;q=1.0',
          [
            new MediaType('gzip', [], 1000.0),
            new MediaType('identity', ['q' => '0.5'], 500.0),
            new MediaType('*', ['q' => '0'], 0.0),
          ],
        ];
    }
}
