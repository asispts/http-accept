<?php declare(strict_types=1);

namespace HttpAccept;

use HttpAccept\Data\MediaType;
use HttpAccept\Utility\MimeValidator;
use HttpAccept\Utility\Parser;
use HttpAccept\Utility\QValueSorter;

final class AcceptParser
{
    private $parser;

    public function __construct()
    {
        $this->parser = new Parser(new QValueSorter());
        $this->parser->setNameValidator(new MimeValidator());
    }

    /**
     * @return MediaType[]
     */
    public function parse(string $source): array
    {
        return $this->parser->parse($source);
    }
}
