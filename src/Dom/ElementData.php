<?php

declare(strict_types=1);

namespace Genius257\Html\Dom;

class ElementData
{
    /**
     * @param \Genius257\Html\Dom\AttributeList<Attribute> $attributes
     */
    public function __construct(
        public string $tag_name,
        public AttributeList $attributes,
    ) {
    }
}
