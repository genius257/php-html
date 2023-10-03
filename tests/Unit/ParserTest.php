<?php

use Genius257\Html\Dom\Element;
use Genius257\Html\Dom\NamedNodeMap;
use Genius257\Html\Dom\Text;
use Genius257\Html\Parser;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase {
    /**
     * @covers \Genius257\Html\Parser::parse
     */
    public function testParseNormalTag() {
        $actual = Parser::parse('<div></div>');
        $this->assertInstanceOf(Element::class, $actual);
        /** @var Element $actual */
        $this->assertEquals('DIV', $actual->tagName);
        $this->assertInstanceOf(NamedNodeMap::class, $actual->attributes);
        $this->assertFalse(isset($actual->attributes['type']));
        $this->assertEquals(0, $actual->childNodes->count());
    }

    /**
     * @covers \Genius257\Html\Parser::parse
     */
    public function testParseSelfClosingTag() {
        $actual = Parser::parse('<input></input>');
        $this->assertInstanceOf(Element::class, $actual);
        /** @var Element $actual */
        $this->assertEquals('INPUT', $actual->tagName);
        $this->assertInstanceOf(NamedNodeMap::class, $actual->attributes);
        $this->assertFalse(isset($actual->attributes['type']));
        $this->assertEquals(0, $actual->childNodes->count());
    }

    /**
     * @covers \Genius257\Html\Parser::parse
     */
    public function testParseNestedTag() {
        $actual = Parser::parse('<div><div></div></div>');
        $this->assertInstanceOf(Element::class, $actual);
        /** @var Element $actual */
        $this->assertEquals('DIV', $actual->tagName);
        $this->assertInstanceOf(NamedNodeMap::class, $actual->attributes);
        $this->assertFalse(isset($actual->attributes['type']));
        $this->assertEquals(1, $actual->childNodes->count());
        /** @var Element $child */
        $child = $actual->childNodes[0];
        $this->assertEquals('DIV', $child->tagName);
        $this->assertInstanceOf(NamedNodeMap::class, $actual->attributes);
        $this->assertFalse(isset($actual->attributes['type']));
        $this->assertEquals(0, $child->childNodes->count());
    }

    /**
     * @covers \Genius257\Html\Parser::parse
     */
    public function testParseText() {
        $actual = Parser::parse('text');
        $this->assertInstanceOf(Text::class, $actual);
        $this->assertEquals('text', $actual->nodeValue);
    }

    /**
     * @covers \Genius257\Html\Parser::parse
     */
    public function testParseTextInElement() {
        $actual = Parser::parse('<div>text</div>');
        $this->assertInstanceOf(Element::class, $actual);
        /** @var Element $actual */
        $this->assertEquals('DIV', $actual->tagName);
        $this->assertInstanceOf(NamedNodeMap::class, $actual->attributes);
        $this->assertFalse(isset($actual->attributes['type']));
        $this->assertEquals(1, $actual->childNodes->count());
        /** @var Element $child */
        $child = $actual->childNodes[0];
        $this->assertInstanceOf(Text::class, $child);
        $this->assertEquals('text', $child->nodeValue);
    }

    /**
     * @covers \Genius257\Html\Parser::parse
     */
    public function testParseAttributes()
    {
        $actual = Parser::parse('<div type="text" class="test"></div>');
        $this->assertInstanceOf(Element::class, $actual);
        /** @var Element $actual */
        $this->assertEquals('DIV', $actual->tagName);
        $this->assertInstanceOf(NamedNodeMap::class, $actual->attributes);
        $this->assertTrue(isset($actual->attributes['type']));
        $this->assertEquals('text', $actual->attributes['type']->value);
        $this->assertTrue(isset($actual->attributes['class']));
        $this->assertEquals('test', $actual->attributes['class']->value);
    }

    /**
     * @covers \Genius257\Html\Parser::parse
     */
    public function testParseAttributeWithoutAValue()
    {
        $actual = Parser::parse('<div type>text</div>');
        $this->assertInstanceOf(Element::class, $actual);
        /** @var Element $actual */
        $this->assertEquals('DIV', $actual->tagName);
        $this->assertInstanceOf(NamedNodeMap::class, $actual->attributes);
        $this->assertTrue(isset($actual->attributes['type']));
        $this->assertEquals('', $actual->attributes['type']->value);
    }

    /**
     * @covers \Genius257\Html\Parser::parse
     */
    public function testParseAttributeWithoutQuotes()
    {
        $actual = Parser::parse('<div type=text>text</div>');
        $this->assertInstanceOf(Element::class, $actual);
        /** @var Element $actual */
        $this->assertEquals('DIV', $actual->tagName);
        $this->assertInstanceOf(NamedNodeMap::class, $actual->attributes);
        $this->assertTrue(isset($actual->attributes['type']));
        $this->assertEquals('text', $actual->attributes['type']->value);
    }

    /**
     * @covers \Genius257\Html\Parser::parse
     */
    public function testParseAttributeWithQuotes()
    {
        $actual = Parser::parse('<div data-type="text">text</div>');
        $this->assertInstanceOf(Element::class, $actual);
        /** @var Element $actual */
        $this->assertEquals('DIV', $actual->tagName);
        $this->assertInstanceOf(NamedNodeMap::class, $actual->attributes);
        $this->assertTrue(isset($actual->attributes['data-type']));
        $this->assertEquals('text', $actual->attributes['data-type']->value);
    }

    /**
     * @covers \Genius257\Html\Parser::parse
     */
    public function testParseAttributeWithQuotesAndSpaces()
    {
        $actual = Parser::parse('<div data-type="text text">text</div>');
        $this->assertInstanceOf(Element::class, $actual);
        /** @var Element $actual */
        $this->assertEquals('DIV', $actual->tagName);
        $this->assertInstanceOf(NamedNodeMap::class, $actual->attributes);
        $this->assertTrue(isset($actual->attributes['data-type']));
        $this->assertEquals('text text', $actual->attributes['data-type']->value);
    }
}
