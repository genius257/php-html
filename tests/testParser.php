<?php
require_once(__DIR__ . '/../vendor/autoload.php');

use Genius257\Html\Parser;

//$sHTML = '<div class="test">hello world</div>';
$sHTML = '<div class="test">hello world</div>';
//$sHTML = '<div><input type="text"/></div>';
$node = Parser::parse($sHTML);

var_dump($node);
