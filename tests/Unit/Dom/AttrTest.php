<?php

use Genius257\Html\Dom\Attr;
use PHPUnit\Framework\TestCase;

class AttrTest extends TestCase
{
    /**
     * @covers \Genius257\Html\Dom\Attr::__construct
     */
    public function testConstruct()
    {
        $actual = new Attr('Foo', 'Bar');

        $this->assertEquals('foo', $actual->name);
        $this->assertEquals('Bar', $actual->value);
    }
}
