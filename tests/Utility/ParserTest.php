<?php declare(strict_types=1);

namespace HttpAccept\Tests\Utility;

use Generator;
use HttpAccept\Data\MediaType;
use HttpAccept\Utility\Parser;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Throwable;

final class ParserTest extends TestCase
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

        (new Parser())->parse($source);
    }

    public static function invalidDataProvider(): Generator
    {
        yield['', InvalidArgumentException::class, 'Media name is empty'];
        yield['    ;', InvalidArgumentException::class, 'Media name is empty'];
        yield['type/subtype;attr', InvalidArgumentException::class, 'Invalid parameter value'];
        yield['type/subtype;  =value', InvalidArgumentException::class, 'Invalid parameter name'];

        // Don't know if this is a valid format
        yield['type/subtype;attr=value1=value2', InvalidArgumentException::class, 'Invalid parameter format'];
    }

    /**
     * @dataProvider validDataProvider
     *
     * @param array<MediaType> $expected
     */
    public function test_parse_valid_data(string $source, array $expected): void
    {
        $actual = (new Parser())->parse($source);
        $this->assertEquals($expected, $actual);
    }

    public static function validDataProvider(): Generator
    {
        yield['type/subtype', [new MediaType('type/subtype', [], 0)]];
        yield['type/subtype;  ', [new MediaType('type/subtype', [], 0)]];
        yield['type/subtype, type2', [new MediaType('type/subtype', [], 0), new MediaType('type2', [], 0)]];

        // Case-insensitive media name
        yield['en-US, en-us ', [new MediaType('en-us', [], 0)]];

        // Empty parameter value
        yield['type   ;   name  =   ', [new MediaType('type', ['name' => ''], 0)]];

        // Case-insensitive parameter name
        yield['type; name=value1; Name=Value2', [new MediaType('type', ['name' => 'Value2'], 0)]];

        // Quoted parameter value
        yield['type; name=  "1"  ', [new MediaType('type', ['name' => '1'], 0)]];
        yield['type; name=  "test "Quoted" value"  ', [new MediaType('type', ['name' => 'test "Quoted" value'], 0)]];
        yield['type; name=  ""Quoted" value"  ', [new MediaType('type', ['name' => '"Quoted" value'], 0)]];
        yield['type; name=  "Quoted "value""  ', [new MediaType('type', ['name' => 'Quoted "value"'], 0)]];
        yield['type; name=  "Quoted value  ', [new MediaType('type', ['name' => '"Quoted value'], 0)]];
        yield['type; name=  Quoted value"  ', [new MediaType('type', ['name' => 'Quoted value"'], 0)]];
        yield['type; name=  ""  ', [new MediaType('type', ['name' => ''], 0)]];
    }

    /**
     * @dataProvider qvalueDataProvide
     */
    public function test_filter_qvalue(string $source, string $expected): void
    {
        $objs = (new Parser())->parse($source);
        $this->assertSame($expected, $objs[0]->toString());
    }

    public function qvalueDataProvide(): Generator
    {
        yield['*;q=1.0', '*'];
        yield['*;q=1', '*'];
        yield['*;q=0.8', '*;q=0.8'];
    }
}
