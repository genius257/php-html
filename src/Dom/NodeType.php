<?php

declare(strict_types=1);

namespace Genius257\Html\Dom;

use Deprecated;

Enum NodeType: int {
    case ELEMENT_NODE = 1;
    case ATTRIBUTE_NODE = 2;
    case TEXT_NODE = 3;
    case CDATA_SECTION_NODE = 4;
    /** @deprecated */
    case ENTITY_REFERENCE_NODE = 5;
    /** @deprecated */
    case ENTITY_NODE = 6;
    case PROCESSING_INSTRUCTION_NODE = 7;
    case COMMENT_NODE = 8;
    case DOCUMENT_NODE = 9;
    case DOCUMENT_TYPE_NODE = 10;
    case DOCUMENT_FRAGMENT_NODE = 11;
    /** @deprecated */
    case NOTATION_NODE = 12;
}
