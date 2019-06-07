<?php

$xml=("https://lenta.ru/rss");
$xmlDoc = new DOMDocument();
$xmlDoc->load($xml);

$x=$xmlDoc->getElementsByTagName('item');

for ($i=0; $i<=5; $i++) {

  $title=$x->item($i)->getElementsByTagName('title')
  ->item(0)->nodeValue;

  $link=$x->item($i)->getElementsByTagName('link')
  ->item(0)->nodeValue;

  $desc=$x->item($i)->getElementsByTagName('description')
  ->item(0)->nodeValue;

  echo ($title . PHP_EOL);
  echo ($link . PHP_EOL);
  echo ($desc . PHP_EOL);
}