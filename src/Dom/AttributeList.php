<?php

declare(strict_types=1);

namespace Genius257\Html\Dom;

use ArrayObject;

/**
 * @template T of Attribute = Attribute
 * 
 * @extends ArrayObject<string, Attribute>
 */
class AttributeList extends ArrayObject
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
