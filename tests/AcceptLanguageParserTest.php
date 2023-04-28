<?php declare(strict_types=1);

namespace HttpAccept\Tests;

use Generator;
use HttpAccept\AcceptLanguageParser;
use HttpAccept\Data\MediaType;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Throwable;

final class AcceptLanguageParserTest extends TestCase
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

        (new AcceptLanguageParser())->parse($source);
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
        $objs = (new AcceptLanguageParser())->parse($source);
        $this->assertEquals($expected, $objs);
    }

    public static function validDataProvider(): Generator
    {
        yield[
          'es;q=0.3,en;q=0.5,en-US',
          [
            new MediaType('en-us', [], 1000.0),
            new MediaType('en', ['q' => '0.5'], 500.0),
            new MediaType('es', ['q' => '0.3'], 300.0),
          ],
        ];
    }
}
