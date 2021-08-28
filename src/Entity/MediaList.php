<?php declare(strict_types=1);

namespace Xynha\HttpAccept\Entity;

final class MediaList
{

    /** @var array<string,MediaType> */
    private $media = [];

    /** @var array<string,float> */
    private $score = [];

    /** @var string[] */
    private $order;

    public function addMedia(MediaType $media) : self
    {
        $new = clone $this;
        $new->media[$media->name()] = $media;
        $new->score[$media->name()] = $media->score();
        return $new;
    }

    public function count() : int
    {
        return count($this->media);
    }

    public function preferredMedia(int $pos) : ?MediaType
    {
        if (empty($this->order)) {
            uasort($this->score, [$this, 'uasort']);
            $this->order = array_keys($this->score);
        }

        $key = $this->order[$pos] ?? '';
        return $this->media[$key] ?? null;
    }

    private function uasort(float $valA, float $valB) : int
    {
        if ($valA === $valB) {
            return 0;
        }
        return ($valA < $valB) ? 1 : -1;
    }
}
