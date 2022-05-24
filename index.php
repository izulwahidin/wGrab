<?php
require('class.php');
$page = new gogoplay5('https://gogoplay5.com/');
$pageData = $page->minify()->pages();
print_r($pageData);

$stream = new gogoplay5('https://gogoplay5.com/videos/dna-ova-episode-3');
$pageStream = $stream->minify()->stream();
print_r($pageStream);
