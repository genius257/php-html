<?php

use Genius257\Html\Dom\Element;
use Genius257\Html\Dom\NamedNodeMap;
use Genius257\Html\Dom\Text;
use Genius257\Html\Parser;

test('parse normal tag', function () {
    $actual = Parser::parse('<div></div>');
    expect($actual)->toBeInstanceOf(Element::class);
    /** @var Element $actual */
    expect($actual->tagName)->toBe('DIV');
    expect($actual->attributes)->toBeInstanceOf(NamedNodeMap::class);
    expect(isset($actual->node_type->attributes['type']))->toBe(false);
    expect($actual->childNodes->count())->toBe(0);
});

//write pest test for Parser.php
test('parse self closing tag', function () {
    $actual = Parser::parse('<input/>');
    expect($actual)->toBeInstanceOf(Element::class);
    /** @var Element $actual */
    expect($actual->tagName)->toBe('INPUT');
    expect($actual->attributes)->toBeInstanceOf(NamedNodeMap::class);
    expect(isset($actual->node_type->attributes['type']))->toBe(false);
    expect($actual->childNodes->count())->toBe(0);
});

test('parse nested tag', function () {
    $actual = Parser::parse('<div><div></div></div>');
    expect($actual)->toBeInstanceOf(Element::class);
    /** @var Element $actual */
    expect($actual->tagName)->toBe('DIV');
    expect($actual->attributes)->toBeInstanceOf(NamedNodeMap::class);
    expect(isset($actual->attributes['type']))->toBe(false);
    expect($actual->childNodes->count())->toBe(1);
    /** @var Element $child */
    $child = $actual->childNodes[0];
    expect($child->tagName)->toBe('DIV');
    expect($child->attributes)->toBeInstanceOf(NamedNodeMap::class);
    expect(isset($child->attributes['type']))->toBe(false);
    expect($child->childNodes->count())->toBe(0);
    //var_dump($actual);
});

test('parser text', function () {
    /** @var Text */
    $actual = Parser::parse('text');
    expect($actual)->toBeInstanceOf(Text::class);
    expect($actual->nodeValue)->toBe('text');
});

test('parse text in element', function () {
    $actual = Parser::parse('<div>text</div>');
    expect($actual)->toBeInstanceOf(Element::class);
    /** @var Element $actual */
    expect($actual->tagName)->toBe('DIV');
    expect($actual->attributes)->toBeInstanceOf(NamedNodeMap::class);
    expect(isset($actual->attributes['type']))->toBe(false);
    expect($actual->childNodes->count())->toBe(1);
    /** @var Element $child */
    $child = $actual->childNodes[0];
    expect($child->nodeValue)->toBe('text');
});

test('parse attributes', function () {
    $actual = Parser::parse('<div type="text"></div>');
    expect($actual)->toBeInstanceOf(Element::class);
    /** @var Element $actual */
    expect($actual->tagName)->toBe('DIV');
    expect($actual->attributes)->toBeInstanceOf(NamedNodeMap::class);
    expect(isset($actual->attributes['type']))->toBe(true);
    expect($actual->attributes['type']->value)->toBe('text');
});

test('parse attribute without value', function () {
    $actual = Parser::parse('<div type>text</div>');
    expect($actual)->toBeInstanceOf(Element::class);
    /** @var Element $actual */
    expect($actual->tagName)->toBe('DIV');
    expect($actual->attributes)->toBeInstanceOf(NamedNodeMap::class);
    expect(isset($actual->attributes['type']))->toBe(true);
    expect($actual->attributes['type']->value)->toBe("");
});

test('parse attribute without quotes', function () {
    $actual = Parser::parse('<div type=text>text</div>');
    expect($actual)->toBeInstanceOf(Element::class);
    /** @var Element $actual */
    expect($actual->tagName)->toBe('DIV');
    expect($actual->attributes)->toBeInstanceOf(NamedNodeMap::class);
    expect(isset($actual->attributes['type']))->toBe(true);
    expect($actual->attributes['type']->value)->toBe('text');
});

test('parse custom data attribute', function () {
    $actual = Parser::parse('<div data-type="text"></div>');
    expect($actual)->toBeInstanceOf(Element::class);
    /** @var Element $actual */
    expect($actual->tagName)->toBe('DIV');
    expect($actual->attributes)->toBeInstanceOf(NamedNodeMap::class);
    expect(isset($actual->attributes['data-type']))->toBe(true);
    expect($actual->attributes['data-type']->value)->toBe('text');
});
