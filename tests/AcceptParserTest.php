<?php declare(strict_types=1);

use HttpAccept\AcceptParser;
use HttpAccept\Entity\MediaType;
use HttpAccept\Entity\Parameters;
use PHPUnit\Framework\TestCase;

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
        $list  = $this->parser->parse('type/subtype;level');
        $media = $list->preferredMedia(0);

        $this->assertSame('type/subtype', $media->name());
        $this->assertSame('type/subtype', $media->mimetype());
        $this->assertSame(1.0, $media->quality());
        $this->assertSame(1101.0, $media->score());
        $this->assertSame('', $media->parameters()->toString());
        $this->assertSame(0, $media->parameters()->count());
    }

    public function testAsteriskOnly()
    {
        $list  = $this->parser->parse('*');
        $media = $list->preferredMedia(0);

        $this->assertSame('*/*', $media->name());
        $this->assertSame('*/*', $media->mimetype());
        $this->assertSame(1.0, $media->quality());
        $this->assertSame(1.0, $media->score());
        $this->assertSame('', $media->parameters()->toString());
        $this->assertSame(0, $media->parameters()->count());
    }

    public function testParseEmptyQuality()
    {
        $list  = $this->parser->parse('type/subtype;q=');
        $media = $list->preferredMedia(0);

        $this->assertSame('type/subtype', $media->name());
        $this->assertSame('type/subtype', $media->mimetype());
        $this->assertSame(1.0, $media->quality());
        $this->assertSame(1101.0, $media->score());
        $this->assertSame('', $media->parameters()->toString());
        $this->assertSame(0, $media->parameters()->count());
    }

    public function testParseFloatQuality()
    {
        $list  = $this->parser->parse('type/subtype;q=0.5');
        $media = $list->preferredMedia(0);

        $this->assertSame('type/subtype', $media->name());
        $this->assertSame('type/subtype', $media->mimetype());
        $this->assertSame(0.5, $media->quality());
        $this->assertSame(1100.5, $media->score());
        $this->assertSame('', $media->parameters()->toString());
        $this->assertSame(0, $media->parameters()->count());
    }

    public function testParseIntegerQuality()
    {
        $list  = $this->parser->parse('type/subtype;q=1');
        $media = $list->preferredMedia(0);

        $this->assertSame('type/subtype', $media->name());
        $this->assertSame('type/subtype', $media->mimetype());
        $this->assertSame(1.0, $media->quality());
        $this->assertSame(1101.0, $media->score());
        $this->assertSame('', $media->parameters()->toString());
        $this->assertSame(0, $media->parameters()->count());
    }

    public function testParseExtension()
    {
        $list  = $this->parser->parse(' type / subtype ; attr1 = 1 ; attr2 = 2');
        $media = $list->preferredMedia(0);

        $this->assertSame('type/subtype;attr1=1;attr2=2', $media->name());
        $this->assertSame('type/subtype', $media->mimetype());
        $this->assertSame(1.0, $media->quality());
        $this->assertSame(1121.0, $media->score());
        $this->assertSame('attr1=1;attr2=2', $media->parameters()->toString());
        $this->assertSame(2, $media->parameters()->count());
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
        $list = $this->parser->parse('text/html, text/html;attr1=1, */*, text/html;attr1=1;attr2=2, text/*');

        $this->assertSame('text/html;attr1=1;attr2=2', $list->preferredMedia(0)->name());
        $this->assertSame('text/html;attr1=1', $list->preferredMedia(1)->name());
        $this->assertSame('text/html', $list->preferredMedia(2)->name());
        $this->assertSame('text/*', $list->preferredMedia(3)->name());
        $this->assertSame('*/*', $list->preferredMedia(4)->name());
    }

    public function testSortSimilarScore()
    {
        $list = $this->parser->parse('*/*, text/html;attr1=1;attr2=2 , text/*, text/css;attr1=1;attr2=2');

        $this->assertSame('text/html;attr1=1;attr2=2', $list->preferredMedia(0)->name());
        $this->assertSame('text/css;attr1=1;attr2=2', $list->preferredMedia(1)->name());
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

    public function testGetAllMedia()
    {
        $list = $this->parser->parse('*/*;q=1, text/html;q=0.25 , text/*;q=0.75, text/css;q=0.5');

        $expected[] = new MediaType('text/css', 0.5, new Parameters());
        $expected[] = new MediaType('text/html', 0.25, new Parameters());
        $expected[] = new MediaType('text/*', 0.75, new Parameters());
        $expected[] = new MediaType('*/*', 1, new Parameters());

        $this->assertEquals($expected, $list->all());
    }

    public function testSameParameterName()
    {
        $list  = $this->parser->parse('type/subtype;level=1;level=2');
        $media = $list->preferredMedia(0);

        $this->assertSame(1, $media->parameters()->count());
        $this->assertSame('2', $media->parameters()->get('level'));
    }

    public function testGetUndefinedParameterName()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Undefined parameter name');

        $list = $this->parser->parse('type/subtype;level');
        $list->preferredMedia(0)->parameters()->get('level');
    }

    public function testQuotedParameter()
    {
        $list  = $this->parser->parse('type/subtype;quoted="test value"');
        $media = $list->preferredMedia(0);

        $this->assertSame('test value', $media->parameters()->get('quoted'));
    }

    public function testGetAllParameters()
    {
        $list  = $this->parser->parse('type/subtype;attr1=1;attr2=2');
        $media = $list->preferredMedia(0);

        $expected = ['attr1' => '1', 'attr2' => '2'];
        $this->assertSame($expected, $media->parameters()->all());
    }
}
