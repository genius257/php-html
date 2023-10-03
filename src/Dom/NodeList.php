<?php

declare(strict_types=1);

namespace Genius257\Html\Dom;

use ArrayObject;

/**
 * @template-covariant T of Node = Node
 * 
 * @extends ArrayObject<int, T>
 */
class NodeList extends ArrayObject
{
    /**
     * @inheritDoc
     * @param array<int, T> $array
     */
    public function __construct(array $array = [])
    {
        parent::__construct($array);
    }
}
