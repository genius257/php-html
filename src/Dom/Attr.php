<?php

declare(strict_types=1);

namespace Genius257\Html\Dom;

/**
 * An attribute of an element.
 *
 * @see https://developer.mozilla.org/en-US/docs/Web/API/Attr
 */
class Attr extends Node {
    // public readonly string $localName;

    /**
     * The qualified name of an attribute.
     *
     * The name of the attribute, with the namespace prefix, if any, in front of it.
     *
     * @see https://developer.mozilla.org/en-US/docs/Web/API/Attr/name
     */
    public readonly string $name;

    // public readonly string $namespaceURI;

    // public readonly Element $ownerElement;

    // public readonly string $prefix;

    public string $value;

    public function __construct(
        string $name,
        string $value = "",
        // Element $ownerElement
    ) {
        parent::__construct();

        $this->name = strtolower($name);
        $this->value = $value;
        // $this->ownerElement = $ownerElement;
    }
}
