<?php declare(strict_types=1);

namespace HttpAccept\Utility;

use HttpAccept\Data\MediaType;

final class QValueSorter
{
    public function calculate(string $source, ?float $quality): float
    {
        $score   = 1.0;
        $quality = $quality ?? 1.0;
        $parts   = \explode('/', $source);
        $type    = $parts[0];
        $subtype = $parts[1] ?? null;

        if (\trim($type) !== '*') {
            $score = 1000.0;
        }
        if ($subtype !== null && \trim($subtype) !== '*') {
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
