<?php

declare(strict_types=1);

namespace Genius257\Html\Dom;

class Element extends Node
{
    /** @var \Genius257\Html\Dom\NamedNodeMap<Attr> */
    public readonly NamedNodeMap $attributes;

    public string $tagName;

    /**
     * @param \Genius257\Html\Dom\NamedNodeMap<Attr> $attributes
     * @param \Genius257\Html\Dom\NodeList<Node> $children
     */
    public function __construct(string $name, NamedNodeMap $attributes, NodeList $children)
    {
        $this->attributes = $attributes;
        //$this->childNodes = $children;
        // $this->children = $children; //FIXME: implement correctly
        // $this->classList
        // $this->className
        //$this->dataset
        // $this->firstChild
        // $this->firstElementChild
        // $this->id
        // $this->innerHTML
        // $this->innerText
        // $this->lastChild
        // $this->lastElementChild
        // $this->localName
        // $this->nextElementSibling
        // $this->nextSibling
        $this->nodeName = strtoupper($name);
        $this->nodeType = NodeType::ELEMENT_NODE;
        $this->nodeValue = null; //FIXME: implement
        // $this->outerHTML
        // $this->outerText
        // $this->ownerDocument
        // $this->parentElement
        // $this->parentNode
        // $this->part
        // $this->prefix
        // $this->previousElementSibling
        // $this->previousSibling
        // $this->style
        $this->tagName = &$this->nodeName;
        // $this->textContent
        // $this->title

        parent::__construct($children);
    }
}
