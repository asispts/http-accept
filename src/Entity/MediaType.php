<?php declare(strict_types=1);

namespace Xynha\HttpAccept\Entity;

use InvalidArgumentException;

final class MediaType
{

    /** @var string */
    private $name;

    /** @var string */
    private $type;

    /** @var string */
    private $subtype;

    /** @var float|null */
    private $quality;

    /** @var string[] */
    private $params;

    /** @var float */
    private $score;

    /** @param string[] $params */
    public function __construct(string $mime, ?float $quality, array $params)
    {
        list($type, $subtype) = explode('/', $mime);

        if (trim($type) === '' || trim($subtype) === '') {
            throw new InvalidArgumentException('Invalid media-type format');
        }

        $this->type = trim($type);
        $this->subtype = trim($subtype);
        $this->quality = $quality;
        $this->params = $params;

        $this->score = $this->calculateScore($this->type(), $this->subtype(), $this->quality(), $this->parameters());

        $this->name = $this->type() . '/' . $this->subtype();
        if (!empty($this->parameters())) {
            $this->name .= ';' . implode(';', $this->parameters());
        }
    }

    public function type() : string
    {
        return $this->type;
    }

    public function subtype() : string
    {
        return $this->subtype;
    }

    public function mimetype() : string
    {
        return $this->type . '/' . $this->subtype;
    }

    public function quality() : float
    {
        if ($this->quality === null) {
            return 1.0;
        }

        return $this->quality;
    }

    /** @return array<string,string> */
    public function parameters() : array
    {
        return $this->params;
    }

    public function score() : float
    {
        return $this->score;
    }

    public function name() : string
    {
        return $this->name;
    }

    /** @param string[] $params */
    private function calculateScore(string $type, string $subtype, float $quality, array $params) : float
    {
        $score = 0.0;
        if (!empty($type) && $type !== '*') {
            $score = 1000.0;
        }

        if (!empty($subtype) && $subtype !== '*') {
            $score += 100.0;
        }

        $score += count($params) * 10.0;
        $score += $quality * 1.0;

        return $score;
    }
}
