<?php

declare(strict_types=1);

namespace Genius257\Html\Dom;

abstract class CharacterData extends Node {
    public string $data;

    public int $length;

    public function __construct() {
        parent::__construct();
    }

    public function nextElementSibling(): ?Element {
        return null;
    }

    public function previousElementSibling(): ?Element {
        return null;
    }

    public function after(DocumentFragment|DocumentType|Element|CharacterData ...$nodes): void {
        //FIXME: https://developer.mozilla.org/en-US/docs/Web/API/CharacterData/after#exceptions
        
        $index = array_search($this, $this->parentNode->childNodes);

        $after = array_slice($this->parentNode->childNodes, $index + 1, null, true);
        
        foreach (array_values($nodes) as $key => $node) {
            $this->parentNode->childNodes[$index + $key + 1] = $node;
        }

        foreach ($after as $node) {
            $this->parentNode->childNodes[] = $node;
        }
    }

    /** Adds the provided data to the end of the node's current data. */
    public function appendData(string $data): void {
        $this->data .= $data;
    }

    public function before(): void {
        //FIXME: Implement
        //FIXME: https://developer.mozilla.org/en-US/docs/Web/API/CharacterData/before#exceptions
    }

    // public function deleteData()

    // public function insertData()

    // public function remove()

    // public function replaceData()

    // replaceWith()

    // substringData()
}
