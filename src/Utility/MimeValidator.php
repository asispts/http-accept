<?php declare(strict_types=1);

namespace HttpAccept\Utility;

use InvalidArgumentException;

final class MimeValidator
{
    public function validate(string $name): string
    {
        if (\trim($name) === '*') {
            return '*/*';
        }

        [$type, $subtype, $invalidPart] = \array_pad(\array_map('trim', \explode('/', $name)), 3, null);
        if (
            $invalidPart !== null
            || $type === ''
            || $subtype === null
            || $subtype === ''
        ) {
            throw new InvalidArgumentException('Invalid media-type format');
        }

        return $name;
    }
}
