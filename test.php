<?php
require_once 'Library.php';
$book = new Book(1,'title','publisher','author',123,'genre');

$result = $book->add();
echo $result;

assert($result === "Book added Successfully!", "Failed adding book");

$result = $book->searchById(1);

assert($result['title'] === "title", "search failed book id may not be added above"); 
$book ->delete(1);
echo "Unit test passed";
?>