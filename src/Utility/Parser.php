<?php declare(strict_types=1);

namespace HttpAccept\Utility;

use HttpAccept\Data\MediaType;
use InvalidArgumentException;

final class Parser
{
    /**
     * @var ValidatorInterface
     */
    private $nameValidator = null;

    /**
     * @var MimeScore|null
     */
    private $score;

    /**
     * @var ScoreSorter|null
     */
    private $sorter;

    public function __construct(?MimeScore $score = null, ?ScoreSorter $sorter = null)
    {
        $this->score  = $score;
        $this->sorter = $sorter;
    }

    public function setNameValidator(ValidatorInterface $validator): void
    {
        $this->nameValidator = $validator;
    }

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

            if ($this->nameValidator !== null) {
                $name = $this->nameValidator->validate($name);
            }

            $parameters = $this->parseParameters($tokens);
            $quality    = $parameters['q'] ? (float) $parameters['q'] : null;
            $score      = $this->getScore($name, $quality, \count($parameters));
            $mediaType  = new MediaType($name, $parameters, $score);

            $result[$mediaType->toString()] = $mediaType;
        }

        if ($this->sorter !== null) {
            $result = $this->sorter->sort($result);
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

    private function getScore(string $name, ?float $quality, int $totalParameter): float
    {
        if ($this->score === null) {
            return 0.0;
        }

        return $this->score->calculate($name, $quality, $totalParameter);
    }
}
