<?php declare(strict_types=1);

namespace HttpAccept;

use HttpAccept\Data\MediaType;
use HttpAccept\Utility\Parser;
use HttpAccept\Utility\QValueSorter;

final class AcceptLanguageParser
{
    private $parser;

    public function __construct()
    {
        $this->parser = new Parser(null, new QValueSorter());
    }

    /**
     * @return MediaType[]
     */
    public function parse(string $source): array
    {
        return $this->parser->parse($source);
    }
}
