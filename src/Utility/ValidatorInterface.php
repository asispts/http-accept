<?php declare(strict_types=1);

namespace HttpAccept\Utility;

interface ValidatorInterface
{
    public function validate(string $name): string;
}
