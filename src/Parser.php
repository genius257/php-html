<?php

declare(strict_types=1);

namespace Genius257\Html;

use Genius257\Html\Dom\Attr;
use Genius257\Html\Dom\NamedNodeMap;
use Genius257\Html\Dom\Text;
use Genius257\Html\Dom\Element;
use Genius257\Html\Dom\NodeList;

class Parser
{
    public function __construct(
        public int $pos,
        public string $input,
    ) {
    }

    /** Read the current character without consuming it. */
    public function nextChar(): string
    {
        return $this->input[$this->pos];
    }

    /** Do the next characters start with the given string? */
    public function startsWith(string $s): bool
    {
        return str_starts_with(substr($this->input, $this->pos), $s);
    }

    /** Return true if all input is consumed. */
    public function eof(): bool
    {
        return $this->pos >= strlen($this->input);
    }

    /** Return the current character, and advance self.pos to the next character. */
    public function consumeChar(): string
    {
        return $this->input[$this->pos++]; // Currently there is no checking if EOL is reached here, may be intentional.
    }

    /**
     * Consume characters until `test` returns false.
     *
     * @param callable(string):bool $test
     */
    public function consumeWhile(callable $test): string
    {
        $result = "";
        while (!$this->eof() && $test($this->nextChar())) {
            $result .= $this->consumeChar();
        }

        return $result;
    }

    /** Consume and discard zero or more whitespace characters. */
    public function consumeWhitespace(): void
    {
        $this->consumeWhile(ctype_space(...));
    }

    /** Parse a tag or attribute name. */
    public function parseTagName(): string
    {
        return $this->consumeWhile(fn (string $c) => preg_match('/[a-zA-Z0-9_\-]/', $c) === 1);
    }

    /**
     * Parse a single node.
     */
    public function parseNode(): Element|Text
    {
        return match ($this->nextChar()) {
            '<' => $this->parseElement(),
            default => $this->parseText(),
        };
    }

    /**
     * Parse a text node.
     */
    public function parseText(): Text
    {
        return new Text($this->consumeWhile(fn (string $c) => $c !== '<')); //FIXME: might need to be looked at later.
    }

    /**
     * Parse a single element, including its open tag, contents, and closing tag.
     */
    public function parseElement(): Element
    {
        // Opening tag.
        $currentCharacter = $this->consumeChar();
        if ($currentCharacter !== '<') {
            throw new \Exception('No opening tag found');
        }
        $tag_name = $this->parseTagName();
        if ($tag_name === '') {
            throw new \Exception('Empty tag name');
        }
        $attrs = $this->parseAttributes();

        // check for self closing tag
        if ($this->nextChar() === '/') {
            $this->consumeChar();

            if ($this->consumeChar() !== '>') {
                throw new \Exception('Unclosed self closing tag');
            }

            return new Element($tag_name, $attrs, new NodeList());
        }

        if ($this->consumeChar() !== '>') {
            throw new \Exception('Unclosed tag');
        }

        // Contents.
        $children = $this->parseNodes();

        // Closing tag.
        if ($this->consumeChar() !== '<') {
            throw new \Exception('No closing tag found');
        }

        if ($this->consumeChar() !== '/') { // @phpstan-ignore-line
            throw new \Exception('No closing tag found');
        }

        $end_tag_name = $this->parseTagName();
        if ($end_tag_name !== $tag_name) {
            throw new \Exception(sprintf('End tag "%s" does not match start tag "%s"', $end_tag_name, $tag_name));
        }
        if ($this->consumeChar() !== '>') {
            throw new \Exception('Unclosed end tag');
        }

        return new Element($tag_name, $attrs, $children);
    }

    /**
     * Parse a single name="value" pair.
     */
    public function parseAttr(): Attr
    {
        $name = $this->parseTagName();

        if ($name === '') {
            throw new \Exception('Empty attribute name');
        }

        $value = "";

        if ($this->nextChar() === '=') {
            $this->consumeChar();
            $value = $this->parseAttrValue();
        }
        return new Attr(name: $name, value: $value);
    }

    /** Parse a quoted value. */
    public function parseAttrValue(): string
    {
        if (in_array($this->nextChar(), ['"', '\''], true)) {
            $open_quote = $this->consumeChar();
            $value = $this->consumeWhile(fn (string $c) => $c !== $open_quote);
            if ($this->consumeChar() !== $open_quote) {
                throw new \Exception('Unclosed attribute value');
            }
        } else {
            $value = $this->consumeWhile(fn (string $c) => !in_array($c, ['"', '\'', '<', '>', '`', ' ']));
        }

        return $value;
    }

    /**
     * Parse a list of name="value" pairs, separated by whitespace.
     * @return NamedNodeMap<Attr>
     */
    public function parseAttributes(): NamedNodeMap
    {
        /** @var NamedNodeMap<Attr> */
        $attributes = new NamedNodeMap();
        do {
            $this->consumeWhitespace();
            if (in_array($this->nextChar(), ['>', '/'], true)) {
                break;
            }
            $attribute = $this->parseAttr();
            $attributes[$attribute->name] = $attribute;
        } while (1);


        return $attributes;
    }

    /**
     * Parse a sequence of sibling nodes.
     * @return NodeList<Element|Text>
     */
    public function parseNodes(): NodeList
    {
        /** @var NodeList<Element|Text> */
        $nodes = new NodeList();
        do {
            $this->consumeWhitespace();
            if ($this->eof() || $this->startsWith('</')) {
                break;
            }
            $nodes[] = $this->parseNode();
        } while (1);
        return $nodes;
    }

    /**
     * Parse an HTML document and return the root element.
     */
    public static function parse(string $source): Element|Text
    {
        $nodes = (new Parser(pos: 0, input: $source))->parseNodes();

        // If the document contains a root element, just return it. Otherwise, create one.
        if (isset($nodes[0])) {
            return $nodes[0]; // @aphpstan-ignore return.type
        } else {
            /** @var NamedNodeMap<Attr> */
            $attributes = new NamedNodeMap();

            return new Element(
                'html',
                $attributes,
                $nodes
            );
        }
    }
}
