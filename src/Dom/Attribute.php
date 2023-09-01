<?php

declare(strict_types=1);

namespace Genius257\Html\Dom;

class Attribute
{
    public function __construct(
        public string $name,
        public string $value,
    ) {
    }
}
