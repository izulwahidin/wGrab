<?php
require('class.php');



$page = new gogoplay5('https://gogoplay5.com/');
$pageData = $page->minify()->getPage();
$pageData = $page->minify()->getPage()->json();
