<?php declare(strict_types=1);

namespace HttpAccept\Utility;

use HttpAccept\Data\MediaType;

final class ScoreSorter
{
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
