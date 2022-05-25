<?php
require('class.php');



$page = new gogoplay5('https://gogoplay5.com/');
$pageData = $page->minify()->getPage()->arr();
$pageData = $page->minify()->getPage()->json();