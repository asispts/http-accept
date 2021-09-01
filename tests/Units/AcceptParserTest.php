<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Xynha\HttpAccept\AcceptParser;

final class AcceptParserTest extends TestCase
{

    /** @var AcceptParser */
    private $parser;

    protected function setUp(): void
    {
        $this->parser = new AcceptParser();
    }

    public function testEmptySource()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Accept data is empty');

        $this->parser->parse('');
    }

    public function testEmptyMimetype()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid media-type format');

        $this->parser->parse(';q=1');
    }

    public function testInvalidMimetype()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid media-type format');

        $this->parser->parse('mime');
    }

    public function testMissingSubtype()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid media-type format');

        $this->parser->parse('mime/');
    }

    public function testInvalidParameterFormat()
    {
        $list = $this->parser->parse('type/subtype;level');
        $media = $list->preferredMedia(0);

        $this->assertSame('type/subtype', $media->name());
        $this->assertSame('type/subtype', $media->mimetype());
        $this->assertSame([], $media->parameters());
        $this->assertSame(1.0, $media->quality());
        $this->assertSame(1101.0, $media->score());
    }

    public function testAsteriskOnly()
    {
        $list = $this->parser->parse('*');
        $media = $list->preferredMedia(0);

        $this->assertSame('*/*', $media->name());
        $this->assertSame('*/*', $media->mimetype());
        $this->assertSame([], $media->parameters());
        $this->assertSame(1.0, $media->quality());
        $this->assertSame(1.0, $media->score());
    }

    public function testParseEmptyQuality()
    {
        $list = $this->parser->parse('type/subtype;q=');
        $media = $list->preferredMedia(0);

        $this->assertSame('type/subtype', $media->name());
        $this->assertSame('type/subtype', $media->mimetype());
        $this->assertSame([], $media->parameters());
        $this->assertSame(1.0, $media->quality());
        $this->assertSame(1101.0, $media->score());
    }

    public function testParseFloatQuality()
    {
        $list = $this->parser->parse('type/subtype;q=0.5');
        $media = $list->preferredMedia(0);

        $this->assertSame('type/subtype', $media->name());
        $this->assertSame('type/subtype', $media->mimetype());
        $this->assertSame([], $media->parameters());
        $this->assertSame(0.5, $media->quality());
        $this->assertSame(1100.5, $media->score());
    }

    public function testParseIntegerQuality()
    {
        $list = $this->parser->parse('type/subtype;q=1');
        $media = $list->preferredMedia(0);

        $this->assertSame('type/subtype', $media->name());
        $this->assertSame('type/subtype', $media->mimetype());
        $this->assertSame([], $media->parameters());
        $this->assertSame(1.0, $media->quality());
        $this->assertSame(1101.0, $media->score());
    }

    public function testParseExtension()
    {
        $list = $this->parser->parse(' type / subtype ; level = 1 ; level = 2');
        $media = $list->preferredMedia(0);

        $this->assertSame('type/subtype;level=1;level=2', $media->name());
        $this->assertSame('type/subtype', $media->mimetype());
        $this->assertSame(['level=1', 'level=2'], $media->parameters());
        $this->assertSame(1.0, $media->quality());
        $this->assertSame(1121.0, $media->score());
    }

    public function testSimilarMediatype()
    {
        $list = $this->parser->parse('type/subtype, type / subtype');
        $this->assertSame(1, $list->count());

        $list = $this->parser->parse('type/subtype;level=1, type / subtype ; level = 1 ');
        $this->assertSame(1, $list->count());
    }

    public function testGetMediatype()
    {
        $list = $this->parser->parse('type/subtype, type / subtype');

        $this->assertNull($list->preferredMedia(-1));
        $this->assertNotNull($list->preferredMedia(0));
        $this->assertNull($list->preferredMedia(1));
    }

    public function testSortWithoutQuality()
    {
        $list = $this->parser->parse('text/html, text/html;level=1, */*, text/html;level=1;level=2, text/*');

        $this->assertSame('text/html;level=1;level=2', $list->preferredMedia(0)->name());
        $this->assertSame('text/html;level=1', $list->preferredMedia(1)->name());
        $this->assertSame('text/html', $list->preferredMedia(2)->name());
        $this->assertSame('text/*', $list->preferredMedia(3)->name());
        $this->assertSame('*/*', $list->preferredMedia(4)->name());
    }

    public function testSortSimilarScore()
    {
        $list = $this->parser->parse('*/*, text/html;level=1;level=2 , text/*, text/css;level=1;level=2');

        $this->assertSame('text/html;level=1;level=2', $list->preferredMedia(0)->name());
        $this->assertSame('text/css;level=1;level=2', $list->preferredMedia(1)->name());
        $this->assertSame('text/*', $list->preferredMedia(2)->name());
        $this->assertSame('*/*', $list->preferredMedia(3)->name());
    }

    public function testSortWithQuality()
    {
        $list = $this->parser->parse('*/*;q=1, text/html;q=0.25 , text/*;q=0.75, text/css;q=0.5');

        $this->assertSame('text/css', $list->preferredMedia(0)->name());
        $this->assertSame('text/html', $list->preferredMedia(1)->name());
        $this->assertSame('text/*', $list->preferredMedia(2)->name());
        $this->assertSame('*/*', $list->preferredMedia(3)->name());
    }
}
