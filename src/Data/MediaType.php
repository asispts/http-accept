<?php declare(strict_types=1);

namespace HttpAccept\Data;

final class MediaType
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var array<string,string>
     */
    private $parameters;

    /**
     * @param array<string,string> $parameters
     */
    public function __construct(string $name, array $parameters)
    {
        $this->name       = $name;
        $this->parameters = $parameters;
    }

    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return array<string,string>
     */
    public function parameters(): array
    {
        return $this->parameters;
    }

    public function hasParamater(string $key): bool
    {
        return \array_key_exists(\strtolower($key), $this->parameters);
    }

    public function getParameter(string $key): string
    {
        return $this->parameters[$key];
    }
}
