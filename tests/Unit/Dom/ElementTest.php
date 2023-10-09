<?php
use Genius257\Html\Dom\Element;
use Genius257\Html\Dom\NamedNodeMap;
use Genius257\Html\Dom\NodeList;
use PHPUnit\Framework\TestCase;

class ElementTest extends TestCase
{
    /**
     * @covers \Genius257\Html\Dom\Element::__construct
     */
    public function testConstructor()
    {
        $actual = new Element(
            'Foo',
            new NamedNodeMap(),
            new NodeList(),
        );

        $this->assertEquals('FOO', $actual->tagName);
        $this->assertCount(0, $actual->attributes);
        $this->assertCount(0, $actual->childNodes);
        $this->assertNull($actual->parentNode);
    }
}
