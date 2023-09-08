<?php

declare(strict_types=1);

namespace Genius257\Html;

use Genius257\Html\Dom\Attribute;
use Genius257\Html\Dom\AttributeList;
use Genius257\Html\Dom\Node;
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
    public function next_char(): string
    {
        return $this->input[$this->pos];
    }

    /** Do the next characters start with the given string? */
    public function starts_with(string $s): bool
    {
        return str_starts_with(substr($this->input, $this->pos), $s);
    }

    /** Return true if all input is consumed. */
    public function eof(): bool
    {
        return $this->pos >= strlen($this->input);
    }

    /** Return the current character, and advance self.pos to the next character. */
    public function consume_char(): string
    {
        return $this->input[$this->pos++]; // Currently there is no checking if EOL is reached here, may be intentional.
    }

    /**
     * Consume characters until `test` returns false.
     *
     * @param callable(string):bool $test
     */
    public function consume_while(callable $test): string
    {
        $result = "";
        while (!$this->eof() && $test($this->next_char())) {
            $result .= $this->consume_char();
        }

        return $result;
    }

    /** Consume and discard zero or more whitespace characters. */
    public function consume_whitespace(): void
    {
        $this->consume_while(ctype_space(...));
    }

    /** Parse a tag or attribute name. */
    public function parse_tag_name(): string
    {
        return $this->consume_while(fn (string $c) => preg_match('/[a-zA-Z0-9_\-]/', $c) === 1);
    }

    /**
     * Parse a single node.
     * @return Node<mixed>
     */
    public function parse_node(): Node
    {
        return match ($this->next_char()) {
            '<' => $this->parse_element(),
            default => $this->parse_text(),
        };
    }

    /**
     * Parse a text node.
     */
    public function parse_text(): Text
    {
        return new Text($this->consume_while(fn (string $c) => $c !== '<')); //FIXME: might need to be looked at later.
    }

    /**
     * Parse a single element, including its open tag, contents, and closing tag.
     */
    public function parse_element(): Element
    {
        // Opening tag.
        $currentCharacter = $this->consume_char();
        assert($currentCharacter === '<');
        $tag_name = $this->parse_tag_name();
        $attrs = $this->parse_attributes();

        // check for self closing tag
        if ($this->next_char() === '/') {
            $this->consume_char();

            $char = $this->consume_char();
            assert($char === '>');

            return new Element($tag_name, $attrs, new NodeList());
        }

        assert($this->next_char() === '>');
        $this->consume_char();

        // Contents.
        $children = $this->parse_nodes();

        // Closing tag.
        assert($this->next_char() === '<');
        $this->consume_char();
        assert($this->next_char() === '/');
        $this->consume_char();
        $end_tag_name = $this->parse_tag_name();
        assert($end_tag_name === $tag_name);
        assert($this->next_char() === '>');
        $this->consume_char();

        return new Element($tag_name, $attrs, $children);
    }

    /**
     * Parse a single name="value" pair.
     * @return Attribute
     */
    public function parse_attr(): Attribute
    {
        $name = $this->parse_tag_name();
        assert($this->next_char() === '=');
        $this->consume_char();
        $value = $this->parse_attr_value();
        return new Attribute(name: $name, value: $value);
    }

    /** Parse a quoted value. */
    public function parse_attr_value(): string
    {
        $open_quote = $this->consume_char();
        assert($open_quote === '"' || $open_quote === '\'');
        $value = $this->consume_while(fn (string $c) => $c !== $open_quote);
        assert($this->next_char() === $open_quote);
        $this->consume_char();
        return $value;
    }

    /**
     * Parse a list of name="value" pairs, separated by whitespace.
     * @return AttributeList<Attribute>
     */
    public function parse_attributes(): AttributeList
    {
        $attributes = new AttributeList();
        do {
            $this->consume_whitespace();
            if ($this->next_char() === '>' || $this->next_char() === '/') {
                break;
            }
            $attribute = $this->parse_attr();
            $attributes[$attribute->name] = $attribute;
        } while (1);


        return $attributes;
    }

    /**
     * Parse a sequence of sibling nodes.
     * @return NodeList<Node<mixed>>
     */
    public function parse_nodes(): NodeList
    {
        $nodes = new NodeList();
        do {
            $this->consume_whitespace();
            if ($this->eof() || $this->starts_with('</')) {
                break;
            }
            $nodes[] = $this->parse_node();
        } while (1);
        return $nodes;
    }

    /**
     * Parse an HTML document and return the root element.
     * @return Node<mixed>
     */
    public static function parse(string $source): Node
    {
        $nodes = (new Parser(pos: 0, input: $source))->parse_nodes();

        // If the document contains a root element, just return it. Otherwise, create one.
        if (count($nodes) === 1) {
            return $nodes[0]; // @phpstan-ignore return.type
        } else {
            return new Element('html', new AttributeList(), $nodes);
        }
    }
}
