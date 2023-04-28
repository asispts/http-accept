<?php declare(strict_types=1);

namespace HttpAccept\Utility;

use HttpAccept\Data\MediaType;
use InvalidArgumentException;

final class QValueSorter
{
    public function calculate(string $source, ?float $quality): float
    {
        [$type, $subtype, $invalidPart] = \array_pad(\array_map('trim', \explode('/', $source)), 3, null);
        if ($type === '' || $subtype === '' || $invalidPart !== null) {
            throw new InvalidArgumentException('Invalid qvalue name');
        }

        $score   = 1.0;
        $quality = $quality ?? 1.0;
        if ($type !== '*') {
            $score = 1000.0;
        }
        if ($subtype !== null && $subtype !== '*') {
            $score += 100.0;
        }

        $score = $score * $quality;
        return $score;
    }

    /**
     * @param MediaType[] $values
     *
     * @return MediaType[]
     */
    public function sort(array $values): array
    {
        \uasort($values, function (MediaType $obj1, MediaType $obj2) {
            if ($obj1->score() === $obj2->score()) {
                return 0;
            }
            if ($obj1->score() < $obj2->score()) {
                return 1;
            }
            return -1;
        });

        return $values;
    }
}
