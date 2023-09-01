<?php

declare(strict_types=1);

namespace Genius257\Html\Dom;

/**
 * @template-covariant T
 */
abstract class Node
{
    /**
     * @param NodeList<Node<mixed>> $children
     * @param T $node_type
     */
    public function __construct(
        public NodeList $children,
        public mixed $node_type, //FIXME: maybe template type T as typehint instead via PHPDOC?
    ) {
    }
}
