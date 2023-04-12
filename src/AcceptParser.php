<?php declare(strict_types=1);

namespace HttpAccept;

use HttpAccept\Entity\MediaList;
use HttpAccept\Entity\MediaType;
use HttpAccept\Entity\Parameters;
use InvalidArgumentException;

final class AcceptParser
{
    public function parse(string $source): MediaList
    {
        if (empty($source)) {
            throw new InvalidArgumentException('Accept data is empty');
        }

        $list  = new MediaList();
        $parts = \explode(',', $source);

        foreach ($parts as $key) {
            $key   = \trim($key);
            $media = $this->parseMediaType($key);
            $list  = $list->addMedia($media);
        }

        return $list;
    }

    private function parseMediaType(string $source): MediaType
    {
        if ($source === '*') {
            $source = '*/*';
        }
        $parts = \explode(';', $source);
        $mime  = \trim((string) \array_shift($parts));

        if ($mime === '' || \strpos($mime, '/') === false) {
            throw new InvalidArgumentException('Invalid media-type format');
        }

        $quality    = null;
        $parameters = new Parameters();
        foreach ($parts as $item) {
            $tparams = \explode('=', $item);
            $key     = \trim($tparams[0] ?? '');
            $value   = \trim($tparams[1] ?? '');

            switch (true) {
                case $key === 'q':
                    $quality = !empty($value) ? (float) $value : null;
                    break;
                case !empty($key) && !empty($value):
                    $parameters->add($key, \trim($value, '"'));
                    break;
            }
        }

        return new MediaType($mime, $quality, $parameters);
    }
}
