<?php

declare(strict_types=1);

namespace Genius257\Html\Dom;

abstract class Node
{
    // public string $baseURI;

    public readonly NodeList $childNodes;

    public function firstChild(): ?Node
    {
        return $this->childNodes[0];
    }

    // public readonly bool $isConnected;

    public function lastChild(): ?Node
    {
        return $this->childNodes[$this->childNodes->count() - 1];
    }

    public function nextSibling(): ?Node
    {
        $parentNodes = $this->parentNode->childNodes;
        return $parentNodes[array_search($this, $parentNodes) + 1] ?? null;
    }

    public string $nodeName;

    public NodeType $nodeType;

    public null|string $nodeValue;

    // public readonly string $ownerDocument;

    public readonly null|Node $parentNode;

    public readonly null|Node $parentElement;

    public function previousSibling(): ?Node
    {
        $parentNodes = $this->parentNode->childNodes;
        return $parentNodes[array_search($this, $parentNodes) - 1] ?? null;
    }

    public function textContent(): string
    {
        return $this->nodeValue ?? "";
    }

    /**
     * @param NodeList<Node> $childNodes
     */
    public function __construct(
        NodeList $childNodes = new NodeList(),
    ) {
        $this->childNodes = $childNodes;
    }

    public function appendChild(Node $node): void
    {
        $this->childNodes[] = $node;
    }

    public function cloneNode(): Node {
        return clone $this;
    }

    // compareDocumentPosition()

    public function contains(Node $node): bool
    {
        return $this->childNodes->contains($node);
    }

    // getRootNode()

    public function hasChildNodes(): bool
    {
        return $this->childNodes->count() > 0;
    }

    public function insertBefore(): void
    {
        $this->childNodes->insertBefore();
    }

    // isDefaultNamespace()

    // isEqualNode()

    // isSameNode()

    // lookupPrefix()

    // lookupNamespaceURI()

    // normalize()

    public function removeChild(): void
    {
        $this->childNodes->remove();
    }

    public function replaceChild(): void
    {
        $this->childNodes->replace();
    }
}
