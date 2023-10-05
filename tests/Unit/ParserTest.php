<?php

use Genius257\Html\Dom\Attr;
use Genius257\Html\Dom\Element;
use Genius257\Html\Dom\NamedNodeMap;
use Genius257\Html\Dom\NodeList;
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
    public function testParseWithText() {
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
    public function testParseWithAttributes()
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

    /**
     * @covers \Genius257\Html\Parser::parse
     */
    public function testParseWithMultipleRootElements() {
        $actual = Parser::parse('<div></div><div></div>');
        $this->assertInstanceOf(Element::class, $actual);
        /** @var Element $actual */
        $this->assertEquals('HTML', $actual->tagName);
        $this->assertInstanceOf(NamedNodeMap::class, $actual->attributes);
        $this->assertCount(0, $actual->attributes);
        $this->assertEquals(2, $actual->childNodes->count());
    }

    /**
     * @covers \Genius257\Html\Parser::__construct
     */
    public function testConstructor()
    {
        $parser = new Parser(
            pos: 0,
            input: '<div></div>',
        );

        $this->assertEquals(0, $parser->pos);
        $this->assertEquals('<div></div>', $parser->input);
    }

    /**
     * @covers \Genius257\Html\Parser::nextChar
     */
    public function testNextChar()
    {
        $parser = new Parser(
            pos: 0,
            input: '<div></div>',
        );

        $this->assertEquals('<', $parser->nextChar());
        $parser->pos = 1;
        $this->assertEquals('d', $parser->nextChar());
        $parser->pos = 3;
        $this->assertEquals('v', $parser->nextChar());
    }

    /**
     * @covers \Genius257\Html\Parser::startsWith
     */
    public function testStarsWith()
    {
        $parser = new Parser(
            pos: 0,
            input: '<div></div>',
        );

        $this->assertTrue($parser->startsWith('<div'));
        $this->assertFalse($parser->startsWith('div'));
        $parser->pos = 3;
        $this->assertTrue($parser->startsWith('v></di'));
    }

    /**
     * @covers \Genius257\Html\Parser::eof
     */
    public function testEof()
    {
        $parser = new Parser(
            pos: 0,
            input: '<div></div>',
        );

        $this->assertFalse($parser->eof());
        $parser->pos = 3;
        $this->assertFalse($parser->eof());
        $parser->pos = 11;
        $this->assertTrue($parser->eof());
        $parser->pos = 100;
        $this->assertTrue($parser->eof());
    }

    /**
     * @covers \Genius257\Html\Parser::consumeChar
     */
    public function testConsumeChar()
    {
        $parser = new Parser(
            pos: 0,
            input: '<div></div>',
        );

        $this->assertEquals('<', $parser->consumeChar());
        $this->assertEquals(1, $parser->pos);
        $this->assertEquals('d', $parser->consumeChar());
        $this->assertEquals(2, $parser->pos);
        $this->assertEquals('i', $parser->consumeChar());
        $this->assertEquals(3, $parser->pos);
        $parser->pos = 6;
        $this->assertEquals('/', $parser->consumeChar());
        $this->assertEquals(7, $parser->pos);
    }

    /**
     * @covers \Genius257\Html\Parser::consumeWhile
     */
    public function testConsumeWhile()
    {
        $parser = new Parser(
            pos: 0,
            input: '<div></div>',
        );

        $this->assertEquals('<div', $parser->consumeWhile(fn(string $c) => $c !== '>'));
        $this->assertEquals(4, $parser->pos);
        $this->assertEquals('></div>', $parser->consumeWhile(fn(string $c) => $c !== '!')); // Verifies that it stops at EOL.
        $this->assertEquals(11, $parser->pos);
        $this->assertEquals('', $parser->consumeWhile(fn(string $c) => $c !== '>')); // Verifies that it stops does not try to advance if at EOL.
        $this->assertEquals(11, $parser->pos);
    }

    /**
     * @covers \Genius257\Html\Parser::consumeWhitespace
     */
    public function testConsumeWhitespace()
    {
        $parser = new Parser(
            pos: 0,
            input: ' <div>      </div>',
        );

        $parser->consumeWhitespace();
        $this->assertEquals(1, $parser->pos);

        $parser->consumeWhitespace();
        $this->assertEquals(1, $parser->pos);// Verifies that it does not advance, if no whitespace was found.

        $parser->pos = 6;
        $parser->consumeWhitespace();
        $this->assertEquals(12, $parser->pos);
    }

    /**
     * @covers \Genius257\Html\Parser::parseTagName
     */
    public function testParseTagName()
    {
        $parser = new Parser(
            pos: 0,
            input: '<div class="test"></div>',
        );

        $actual = $parser->parseTagName();
        $this->assertEquals('', $actual);
        $this->assertEquals(0, $parser->pos);
        $parser->pos = 1;
        $actual = $parser->parseTagName();
        $this->assertEquals('div', $actual);
        $this->assertEquals(4, $parser->pos);
    }

    /**
     * @covers \Genius257\Html\Parser::parseNode
     */
    public function testParseNode()
    {
        $parser = new Parser(
            pos: 0,
            input: '<div></div>text',
        );

        $actual = $parser->parseNode();
        $this->assertEquals(11, $parser->pos);
        $this->assertInstanceOf(Element::class, $actual);
        $this->assertEquals('DIV', $actual->tagName);

        $actual = $parser->parseNode();
        $this->assertEquals(15, $parser->pos);
        $this->assertInstanceOf(Text::class, $actual);
        $this->assertEquals('text', $actual->data);
    }

    /**
     * @covers \Genius257\Html\Parser::parseText
     */
    public function testParseText()
    {
        $parser = new Parser(
            pos: 0,
            input: '<div>text</div>',
        );

        $actual = $parser->parseText();
        $this->assertEquals('', $actual->data);
        $this->assertEquals(0, $parser->pos);

        $parser->pos = 5;
        $actual = $parser->parseText();
        $this->assertEquals('text', $actual->data);
        $this->assertEquals(9, $parser->pos);
    }

    /**
     * @covers \Genius257\HTML\Parser::parseElement
     */
    public function testParseElement()
    {
        $parser = new Parser(
            pos: 0,
            input: '<div class="test"></div>',
        );

        $actual = $parser->parseElement();
        $this->assertInstanceOf(Element::class, $actual);
        $this->assertEquals('DIV', $actual->tagName);
        $this->assertCount(1, $actual->attributes);
        $this->assertEquals('test', $actual->attributes['class']->value);
        $this->assertEquals(24, $parser->pos);
    }

    /**
     * @covers \Genius257\Html\Parser::parseElement
     */
    public function testParseElementWihtoutOpeningTag()
    {
        $parser = new Parser(
            pos: 0,
            input: 'text',
        );

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('No opening tag found');

        $parser->parseElement();
    }

    /**
     * @covers \Genius257\Html\Parser::parseElement
     */
    public function testParseElementWithEmptyTag()
    {
        $parser = new Parser(
            pos: 0,
            input: '<>',
        );

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Empty tag name');

        $parser->parseElement();
    }

    /**
     * @covers \Genius257\Html\Parser::parseElement
     */
    public function testParseElementWithSelfClosingTag()
    {
        $parser = new Parser(
            pos: 0,
            input: '<input class="test"/>',
        );

        $actual = $parser->parseElement();
        $this->assertInstanceOf(Element::class, $actual);
        $this->assertEquals('INPUT', $actual->tagName);
        $this->assertCount(1, $actual->attributes);
        $this->assertEquals('test', $actual->attributes['class']->value);
    }

    /**
     * @covers \Genius257\Html\Parser::parseElement
     */
    public function testParseElementWithUnclosedSelfClosingTag()
    {
        $parser = new Parser(
            pos: 0,
            input: '<input/',
        );

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unclosed self closing tag');

        $parser->parseElement();
    }

    /**
     * @covers \Genius257\Html\Parser::parseElement
     */
    public function testParseElementWithUnclosedTag()
    {
        $parser = new Parser(
            pos: 0,
            input: '<input class="test"',
        );

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unclosed tag');

        $parser->parseElement();
    }

    /**
     * @covers \Genius257\Html\Parser::parseElement
     */
    public function testParseElementWithUnclosedTagAndClosingTag()
    {
        $parser = new Parser(
            pos: 0,
            input: '<input class="test"</input>',
        );

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unclosed tag');

        $parser->parseElement();
    }

    /**
     * @covers \Genius257\Html\Parser::parseElement
     */
    public function testParseElementWithMissingClosingTag()
    {
        $parser = new Parser(
            pos: 0,
            input: '<div>test',
        );

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('No closing tag found');

        $parser->parseElement();
    }

    /**
     * @covers \Genius257\Html\Parser::parseElement
     */
    public function testParseElementWithoutClosingTag()
    {
        $parser = new Parser(
            pos: 0,
            input: '<div><div>',
        );

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('No closing tag found');

        $parser->parseElement();
    }

    /**
     * @covers \Genius257\Html\Parser::parseElement
     */
    public function testParseElementWithMismatchingClosingTag()
    {
        $parser = new Parser(
            pos: 0,
            input: '<div></span>',
        );

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('End tag "span" does not match start tag "div"');

        $parser->parseElement();
    }

    /**
     * @covers \Genius257\Html\Parser::parseElement
     */
    public function testParseElementWithUnclosedEndTag()
    {
        $parser = new Parser(
            pos: 0,
            input: '<div></div',
        );

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unclosed end tag');

        $parser->parseElement();
    }

    /**
     * @covers \Genius257\Html\Parser::parseAttr
     */
    public function testParseAttr()
    {
        $parser = new Parser(
            pos: 5,
            input: '<div class="test">',
        );

        $actual = $parser->parseAttr();

        $this->assertInstanceOf(Attr::class, $actual);
        $this->assertEquals('class', $actual->name);
        $this->assertEquals('test', $actual->value);
        $this->assertEquals(17, $parser->pos);
    }

    /**
     * @covers \Genius257\Html\Parser::parseAttr
     */
    public function testParseAttrWithEmptyName()
    {
        $parser = new Parser(
            pos: 5,
            input: '<div ="test">',
        );

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Empty attribute name');

        $parser->parseAttr();
    }

    /**
     * @covers \Genius257\Html\Parser::parseAttr
     */
    public function testParseAttrWithoutValue()
    {
        $parser = new Parser(
            pos: 5,
            input: '<div class>',
        );

        $actual = $parser->parseAttr();

        $this->assertInstanceOf(Attr::class, $actual);
        $this->assertEquals('class', $actual->name);
        $this->assertEquals('', $actual->value);
        $this->assertEquals(10, $parser->pos);
    }

    /**
     * @covers \Genius257\Html\Parser::parseAttrValue
     */
    public function testParseAttrValue()
    {
        $parser = new Parser(
            pos: 11,
            input: '<div class="test">',
        );

        $actual = $parser->parseAttrValue();

        $this->assertEquals('test', $actual);
        $this->assertEquals(17, $parser->pos);

        $parser = new Parser(
            pos: 11,
            input: '<div class=\'test\'>',
        );

        $actual = $parser->parseAttrValue();

        $this->assertEquals('test', $actual);
        $this->assertEquals(17, $parser->pos);
    }

    /**
     * @covers \Genius257\Html\Parser::parseAttrValue
     */
    public function testParseAttrValueWithoutQuotes()
    {
        $parser = new Parser(
            pos: 11,
            input: '<div class=test>',
        );

        $actual = $parser->parseAttrValue();

        $this->assertEquals('test', $actual);
        $this->assertEquals(15, $parser->pos);
    }

    /**
     * @covers \Genius257\Html\Parser::parseAttributes
     */
    public function testParseAttributes()
    {
        $parser = new Parser(
            pos: 4,
            input: '<div id="test" class="test" style="test"></div>',
        );

        $actual = $parser->parseAttributes();

        $this->assertInstanceOf(NamedNodeMap::class, $actual);
        $this->assertCount(3, $actual);
        $this->assertEquals('test', $actual['id']->value);
        $this->assertEquals('test', $actual['class']->value);
        $this->assertEquals('test', $actual['style']->value);
        $this->assertEquals(40, $parser->pos);
    }

    /**
     * @covers \Genius257\Html\Parser::parseNodes
     */
    public function testParseNodes()
    {
        $parser = new Parser(
            pos: 0,
            input: '<div id="A"><span>a</span></div> <div id="B">b</div><div id="C">c</div>',
        );

        $actual = $parser->parseNodes();

        $this->assertInstanceOf(NodeList::class, $actual);
        $this->assertCount(3, $actual);

        $this->assertInstanceOf(Element::class, $actual[0]);
        $this->assertEquals('DIV', $actual[0]->tagName);
        $this->assertEquals('A', $actual[0]->attributes['id']->value);

        $this->assertInstanceOf(Element::class, $actual[1]);
        $this->assertEquals('DIV', $actual[1]->tagName);
        $this->assertEquals('B', $actual[1]->attributes['id']->value);

        $this->assertInstanceOf(Element::class, $actual[2]);
        $this->assertEquals('DIV', $actual[2]->tagName);
        $this->assertEquals('C', $actual[2]->attributes['id']->value);
    }
}
