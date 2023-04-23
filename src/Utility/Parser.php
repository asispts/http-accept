<?php declare(strict_types=1);

namespace HttpAccept\Utility;

use HttpAccept\Data\MediaType;
use InvalidArgumentException;

final class Parser
{
    /**
     * @return array<MediaType>
     */
    public function parse(string $source): array
    {
        $result = [];
        $parts  = \explode(',', $source);

        foreach ($parts as $item) {
            $tokens = \explode(';', $item);
            $name   = \strtolower(\trim(\array_shift($tokens)));

            if (empty($name)) {
                throw new InvalidArgumentException('Media name is empty');
            }

            $parameters    = $this->parseParameters($tokens);
            $result[$name] = new MediaType($name, $parameters);
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
            if (\trim($item) === '') {
                continue;
            }

            $parts = \explode('=', $item);
            $name  = $parts[0] ?? null;
            $value = $parts[1] ?? null;

            if (\count($parts) > 2) {
                throw new InvalidArgumentException('Invalid parameter format');
            }

            if ($name === null || \trim($name) === '') {
                throw new InvalidArgumentException('Invalid parameter name');
            }

            if ($value === null) {
                throw new InvalidArgumentException('Invalid parameter value');
            }

            $result[\strtolower(\trim($name))] = $this->normalizeQuotedString(\trim($value));
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
}
