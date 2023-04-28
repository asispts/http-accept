<?php declare(strict_types=1);

namespace HttpAccept\Tests\Utility;

use Generator;
use HttpAccept\Data\MediaType;
use HttpAccept\Utility\QValueSorter;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class QValueSorterTest extends TestCase
{
    /**
     * @dataProvider invalidDataProvider
     */
    public function test_invalid_data(string $source): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid qvalue name');

        (new QValueSorter())->calculate($source, null);
    }

    public static function invalidDataProvider(): Generator
    {
        yield['  '];
        yield['type/  '];
        yield['type/subtype/error'];
    }

    /**
     * @dataProvider scoreDataProvider
     */
    public function test_calculate_score(string $mime, ?float $quality, float $expected): void
    {
        $actual = (new QValueSorter())->calculate($mime, $quality);
        $this->assertSame($expected, $actual);
    }

    public static function scoreDataProvider(): Generator
    {
        yield['  *  ', null, 1.0];
        yield['*/*', null, 1.0];
        yield['en-US', null, 1000.0];
        yield['en-US', 0.8, 800.0];
        yield['type/subtype', null, 1100.0];
        yield['type/*', null, 1000.0];

        yield['en-US', 0, 0.0];
    }

    /**
     * @dataProvider sortDataProvider
     *
     * @param MediaType[] $types
     * @param string[] $expected
     */
    public function test_sort_qvalue(array $types, array $expected): void
    {
        $result = (new QValueSorter())->sort($types);
        foreach ($result as $item) {
            $this->assertEquals($item, \array_shift($expected));
        }
    }

    public static function sortDataProvider(): Generator
    {
        yield [
          [
            $first = new MediaType('first', [], 100),
            $last  = new MediaType('last', [], 100),
          ],
          [
            $first,
            $last,
          ],
        ];

        yield[
          [
            $first = new MediaType('first', [], 10),
            $last  = new MediaType('last', [], 100),
          ],
          [
            $last,
            $first,
          ],
        ];

        yield[
          [
            $first = new MediaType('first', [], 100),
            $last  = new MediaType('last', [], 10),
          ],
          [
            $first,
            $last,
          ],
        ];
    }
}
