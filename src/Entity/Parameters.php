<?php declare(strict_types=1);

namespace Pts\HttpAccept\Entity;

use InvalidArgumentException;

final class Parameters
{

    /**
     * @var array<string,string>
     */
    private $params = [];

    public function add(string $key, string $value): void
    {
        $this->params[$key] = $value;
    }

    public function count(): int
    {
        return count($this->params);
    }

    public function toString(): string
    {
        $str = [];
        foreach ($this->params as $key => $value) {
            $str[] = $key . '=' . $value;
        }

        return implode(';', $str);
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->params);
    }

    public function get(string $key): string
    {
        if ($this->has($key) === false) {
            throw new InvalidArgumentException('Undefined parameter name');
        }

        return $this->params[$key];
    }

    /**
     * @return array<string,string>
     */
    public function all(): array
    {
        return $this->params;
    }
}
