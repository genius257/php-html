<?php

declare(strict_types=1);

namespace Genius257\Html\Dom;

use ArrayObject;
use Countable;
use IteratorAggregate;

/**
 * @template T
 *
 * @extends ArrayObject<string, T>
 */
class NamedNodeMap extends ArrayObject implements IteratorAggregate, Countable {
    //TODO: https://developer.mozilla.org/en-US/docs/Web/API/AttributeList
}
