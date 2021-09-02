<?php declare(strict_types=1);

namespace Pts\HttpAccept\Entity;

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

        $new->sortByScore();

        return $new;
    }

    public function count() : int
    {
        return count($this->media);
    }

    public function preferredMedia(int $pos) : ?MediaType
    {
        $key = $this->order[$pos] ?? '';
        return $this->media[$key] ?? null;
    }

    /**
     * @return MediaType[]
     */
    public function all(): array
    {
        $tmp = [];
        foreach ($this->order as $key) {
            $tmp[] = $this->media[$key];
        }

        return $tmp;
    }

    private function sortByScore(): void
    {
        if ($this->order !== null && count($this->order) === count($this->score)) {
            return;
        }

        uasort($this->score, [$this, 'uasort']);
        $this->order = array_keys($this->score);
    }

    private function uasort(float $valA, float $valB) : int
    {
        if ($valA === $valB) {
            return 0;
        }
        return ($valA < $valB) ? 1 : -1;
    }
}
