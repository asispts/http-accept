<?php declare(strict_types=1);

namespace HttpAccept\Utility;

final class MimeScore
{
    public function calculate(string $mime, ?float $quality, int $totalParameter): float
    {
        $score   = 0.0;
        $quality = $quality ?? 1.0;

        list($type, $subtype) = \explode('/', $mime);

        if ($type !== '*') {
            $score = 1000.0;
        }
        if ($subtype !== '*') {
            $score += 100.0;
        }

        $score += $totalParameter * 10.0;
        $score += $quality * 1.0;

        return $score;
    }
}
