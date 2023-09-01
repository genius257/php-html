<?php

declare(strict_types=1);

namespace Genius257\Html\Dom;

/**
 * @extends Node<string>
 */
class Text extends Node
{
    public function __construct(string $data)
    {
        parent::__construct(new NodeList(), $data);
    }
}
