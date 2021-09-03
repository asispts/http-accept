<?php declare(strict_types=1);

namespace HttpAccept\Entity;

use InvalidArgumentException;

final class MediaType
{

    private $name;

    private $type;

    private $subtype;

    private $quality;

    private $parameters;

    /** @var float */
    private $score;

    public function __construct(string $mime, ?float $quality, Parameters $parameters)
    {
        list($type, $subtype) = explode('/', $mime);

        if (trim($type) === '' || trim($subtype) === '') {
            throw new InvalidArgumentException('Invalid media-type format');
        }

        $this->type = trim($type);
        $this->subtype = trim($subtype);
        $this->quality = $quality;
        $this->parameters = $parameters;

        $this->score = $this->calculateScore($this->type, $this->subtype, $this->quality(), $this->parameters);

        $this->name = $this->type . '/' . $this->subtype;
        if ($this->parameters->count() > 0) {
            $this->name .= ';' . $this->parameters->toString();
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

    public function parameters(): Parameters
    {
        return $this->parameters;
    }

    public function score() : float
    {
        return $this->score;
    }

    public function name() : string
    {
        return $this->name;
    }

    private function calculateScore(string $type, string $subtype, float $quality, Parameters $param) : float
    {
        $score = 0.0;
        if (!empty($type) && $type !== '*') {
            $score = 1000.0;
        }

        if (!empty($subtype) && $subtype !== '*') {
            $score += 100.0;
        }

        $score += $param->count() * 10.0;
        $score += $quality * 1.0;

        return $score;
    }
}
