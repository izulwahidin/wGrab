<?php
require('class.php');



$page = new gogoplay5('https://gogoplay5.com/');
$pageData = $page->minify()->getPage(); // hasil yang diharapkan berupa array dari fungsi page()
// $pageData = $page->minify()->getPage()->json(); // hasil sudah oke berupa json

print_r($pageData);