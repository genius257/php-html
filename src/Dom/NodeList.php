<?php

declare(strict_types=1);

namespace Genius257\Html\Dom;

use ArrayObject;

/**
 * @template T of Node = Node
 * 
 * @extends ArrayObject<int, T>
 */
class NodeList extends ArrayObject
{
    /**
     * @inheritDoc
     * @param array<string, T> $array
     */
    public function __construct(array $array = [])
    {
        parent::__construct($array);
    }
}
