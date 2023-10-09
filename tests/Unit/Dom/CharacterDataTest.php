<?php

use Genius257\Html\Dom\CharacterData;
use Genius257\Html\Dom\Element;
use Genius257\Html\Dom\NamedNodeMap;
use Genius257\Html\Dom\NodeList;
use Genius257\Html\Dom\Text;
use PHPUnit\Framework\TestCase;

class CharacterDataTest extends TestCase
{
    public function makeCharacterData(): CharacterData
    {
        return new class () extends CharacterData {
            public string $data = "";
        };
    }

    /**
     * @covers \Genius257\Html\Dom\CharacterData::__construct
     */
    public function testConstructor()
    {
        $actual = $this->makeCharacterData();

        // This test only checks that the constructor works, not that it does anything.
        $this->assertEquals(1, 1);
    }

    /**
     * @covers \Genius257\Html\Dom\CharacterData::nextElementSibling
     */
    public function testNextElementSibling()
    {
        $actual = $this->makeCharacterData();

        $this->assertNull($actual->nextElementSibling());
    }

    /**
     * @covers \Genius257\Html\Dom\CharacterData::previousElementSibling
     */
    public function testPreviousElementSibling()
    {
        $actual = $this->makeCharacterData();

        $this->assertNull($actual->previousElementSibling());
    }

    /**
     * @covers \Genius257\Html\Dom\CharacterData::after
     */
    public function testAfter()
    {
        $characterData = $this->makeCharacterData();
        $characterData->parentNode = new Element(
            'div',
            new NamedNodeMap(),
            new NodeList([
                new Text('foo'),
                $characterData,
                new Text('bar'),
            ]),
        );
        $node1 = $this->createMock(CharacterData::class);
        $node2 = $this->createMock(CharacterData::class);
        
        $characterData->after($node1, $node2);
        
        $this->assertSame($characterData, $node1->parentNode);
        $this->assertSame($characterData, $node2->parentNode);

        $this->assertCount(4, $characterData->parentNode->childNodes);

        $this->assertSame($characterData, $characterData->parentNode->childNodes[1]);
        $this->assertSame($node1, $characterData->parentNode->childNodes[2]);
        $this->assertSame($node2, $characterData->parentNode->childNodes[3]);
    }

    /**
     * @covers \Genius257\Html\Dom\CharacterData::after
     */
    public function testAfterWithOutParent()
    {
        $characterData = $this->makeCharacterData();
        $node1 = $this->createMock(CharacterData::class);
        $node2 = $this->createMock(CharacterData::class);

        $characterData->after($node1, $node2);

        $this->assertNull($characterData->parentNode);
    }

    /**
     * @covers \Genius257\Html\Dom\CharacterData::appendData
     */
    public function testAppendData()
    {
        $characterData = $this->makeCharacterData();

        $characterData->appendData('foo');

        $this->assertSame('foo', $characterData->data);

        $characterData->appendData('bar');

        $this->assertSame('foobar', $characterData->data);
    }

    /**
     * @covers \Genius257\Html\Dom\CharacterData::before
     */
    public function testBefore()
    {
        $characterData = $this->makeCharacterData();
        $characterData->parentNode = new Element(
            'div',
            new NamedNodeMap(),
            new NodeList([
                new Text('foo'),
                $characterData,
                new Text('bar'),
            ]),
        );
        $node1 = $this->createMock(CharacterData::class);
        $node2 = $this->createMock(CharacterData::class);
        
        $characterData->before($node1, $node2);
        
        $this->assertSame($characterData, $node1->parentNode);
        $this->assertSame($characterData, $node2->parentNode);

        $this->assertCount(4, $characterData->parentNode->childNodes);

        $this->assertSame($characterData, $characterData->parentNode->childNodes[3]);
        $this->assertSame($node2, $characterData->parentNode->childNodes[2]);
        $this->assertSame($node1, $characterData->parentNode->childNodes[1]);
    }
}
