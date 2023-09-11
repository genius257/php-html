<?php

declare(strict_types=1);

namespace Genius257\Html\Dom;

class Text extends CharacterData
{
    public string $nodeName = "#text";
    public NodeType $nodeType = NodeType::TEXT_NODE;

    public function __construct(string $data)
    {
        parent::__construct();
        $this->data = $data;
        $this->nodeValue = &$this->data;
    }

    public function wholeText(): string
    {
        $wholeText = $this->data;

        while ($sibling = $this->previousSibling()) {
            if (!($sibling instanceof Text)) {
                break;
            }

            $wholeText = $sibling->data.$wholeText;
        }

        while ($sibling = $this->nextSibling()) {
            if (!($sibling instanceof Text)) {
                break;
            }

            $wholeText .= $sibling->data;
        }

        return $wholeText;
    }

    // splitText()
}
