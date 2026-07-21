<?php

namespace App\Domain\Decision;

class Decision
{
    public function __construct(
        public string $action,
        public ?string $reason = null,
        public array $meta = [],
    ) {}
}