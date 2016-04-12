<?php
// https://www.clusterpoint.com/docs/api/4/php/386/quick-usage-example

//include Clusterpoint Library

include 'Clusterpoint.php';
//include 'vendor/autoload.php'; // if using with Composer

//Note, replace 'api-eu' with 'api-us', if you use US Cloud server
$config = array(
	'host' => 'https://api-eu.clusterpoint.com/v4/',
	'account_id' => 'ACCOUNT_ID',
	'username' => 'USERNAME',
	'password' => 'PASSWORD',
	'debug' => true
);
$cp = new Clusterpoint\Client($config);

// In this example we will use a simple database named "bookshelf" which consists of books and book authors.

// the select a collection to work with
$booksCollection = $cp->database('bookshelf.books');

// you can use $cp instance multiple times to access any collection
$authorsCollection = $cp->database('bookshelf.authors');

// INSERT books
$documents = [
	[
		'_id' => 1,
		'title' => 'Book 1',
		'category' => 'Science',
		'author_id' => 1,
	],
	[
		'_id' => 2,
		'title' => 'Book 2',
		'category' => 'Fiction',
		'author_id' => 2,
	]
];
$booksCollection->insertMany($documents);


// INSERT book authors
$documents = [
	[
		'_id' => 1,
		'name' => 'John',
	],
	[
		'_id' => 2,
		'name' => 'Fred',
	]
];
$authorsCollection->insertMany($documents);

// list all authors
$authors = $authorsCollection->get();
echo $authors->executedQuery(); // JS/SQL:  SELECT * FROM authors LIMIT 0, 20
foreach ($authors as $author) {
	echo $author->name . '<br/>';
}

// list books with authors using JOIN (currently you have to use raw() function for JOINS)
$books = $booksCollection->raw('SELECT *, authors.name
        FROM books
        LEFT JOIN authors ON authors._id == books.author_id');
foreach ($books as $book) {
	echo $book->title . ' (' . $book->{'authors.name'} . ')<br/>';
}


// Another query builder example:
$results = $booksCollection->select(['name', 'color', 'price', 'category'])
	->where('color', 'red')
	->where('availability', true)
	->groupBy('category')
	->orderBy('price')
	->limit(5);

echo $results->getQuery();
