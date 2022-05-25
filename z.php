<?php
require('class.php');



$page = new gogoplay5('https://gogoplay5.com/');
$pageData = $page->minify()->page()->json(); //WORK
$pageData = $page->minify()->getPage(); //NULL
var_dump($pageData);