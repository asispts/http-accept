<?php declare(strict_types=1);

namespace HttpAccept;

use HttpAccept\Data\MediaType;
use HttpAccept\Utility\MimeValidator;
use HttpAccept\Utility\Parser;
use InvalidArgumentException;

final class ContentTypeParser
{
    private $parser;

    public function __construct()
    {
        $this->parser = new Parser();
        $this->parser->setNameValidator(new MimeValidator());
    }

    public function parse(string $source): MediaType
    {
        $types = $this->parser->parse($source);

        if (\count($types) !== 1) {
            throw new InvalidArgumentException('Invalid Content-Type format');
        }

        return \array_pop($types);
    }
}
