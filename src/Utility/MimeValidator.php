<?php declare(strict_types=1);

namespace HttpAccept\Utility;

use InvalidArgumentException;

final class MimeValidator
{
    public function validate(string $name): string
    {
        if ($name === '*') {
            return '*/*';
        }

        $parts   = \explode('/', $name);
        $type    = $parts[0] ?? null;
        $subtype = $parts[1] ?? null;

        if (
            \count($parts) > 2
            || $type === null
            || \trim($type) === ''
            || $subtype === null
            || \trim($subtype) === ''
        ) {
            throw new InvalidArgumentException('Invalid media-type format');
        }

        return $name;
    }
}
