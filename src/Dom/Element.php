<?php

declare(strict_types=1);

namespace Genius257\Html\Dom;

/**
 * @extends Node<ElementData>
 */
class Element extends Node
{
    /**
     * @param \Genius257\Html\Dom\AttributeList<Attribute> $attributes
     * @param \Genius257\Html\Dom\NodeList<Node<mixed>> $children
     */
    public function __construct(string $name, AttributeList $attributes, NodeList $children)
    {
        parent::__construct($children, new ElementData($name, $attributes));
    }
}
