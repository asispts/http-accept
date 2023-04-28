<?php declare(strict_types=1);

namespace HttpAccept\Utility;

use HttpAccept\Data\MediaType;
use InvalidArgumentException;

final class Parser
{
    private $mimeValidator;

    private $qvalue;

    public function __construct(?MimeValidator $mimeValidator = null, ?QValueSorter $qvalue = null)
    {
        $this->mimeValidator = $mimeValidator;
        $this->qvalue        = $qvalue;
    }

    /**
     * @return array<MediaType>
     */
    public function parse(string $source): array
    {
        $result = [];
        $parts  = \explode(',', $source);

        foreach ($parts as $item) {
            $tokens = \array_map('trim', \explode(';', $item));

            $name = \strtolower(\array_shift($tokens));
            if (empty($name)) {
                throw new InvalidArgumentException('Media name is empty');
            }

            if ($this->mimeValidator !== null) {
                $name = $this->mimeValidator->validate($name);
            }

            $parameters = $this->parseParameters($tokens);
            $quality    = isset($parameters['q']) ? (float) $parameters['q'] : null;
            $score      = $this->getScore($name, $quality);
            $mediaType  = new MediaType($name, $parameters, $score);

            $result[$mediaType->toString()] = $mediaType;
        }

        if ($this->qvalue !== null) {
            $result = $this->qvalue->sort($result);
        }
        return \array_values($result);
    }

    /**
     * @param string[] $input
     *
     * @return array<string,string>
     */
    private function parseParameters(array $input): array
    {
        $result = [];
        foreach ($input as $item) {
            if ($item === '') {
                continue;
            }

            [$name, $value, $invalidPart] = \array_pad(\array_map('trim', \explode('=', $item)), 3, null);
            if ($invalidPart !== null) {
                throw new InvalidArgumentException('Invalid parameter format');
            }
            if ($name === '') {
                throw new InvalidArgumentException('Invalid parameter name');
            }
            if ($value === null) {
                throw new InvalidArgumentException('Invalid parameter value');
            }

            $name  = \strtolower($name);
            $value = $this->normalizeQuotedString($value);
            if ($name === 'q' && \floatval($value) === 1.0) {
                continue;
            }
            $result[$name] = $value;
        }

        return $result;
    }

    private function normalizeQuotedString(string $value): string
    {
        if (\substr($value, 0, 1) === '"' && \substr($value, -1, 1) === '"') {
            return \substr($value, 1, \strlen($value) - 2);
        }

        return $value;
    }

    private function getScore(string $name, ?float $quality): float
    {
        if ($this->qvalue === null) {
            return 0.0;
        }

        return $this->qvalue->calculate($name, $quality);
    }
}
