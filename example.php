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
	'debug' => false
);

$cp = new Clusterpoint\Client($config);
// In this example we will use a simple database named "bookshelf" which consists of books and book authors.

// the select a collection to work with
$booksCollection = $cp->database('bookshelf.books');

// you can use $cp instance multiple times to access any collection
$authorsCollection = $cp->database('bookshelf.authors');


// try to remove documents just for the purpose of this example
try{
	$booksCollection->delete(1);
	$booksCollection->delete(2);
	$authorsCollection->delete(1);
	$authorsCollection->delete(2);
}
catch (Exception $e){
	// documents did not exist
}

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
echo $authors->executedQuery()."<br/>\r\n"; // JS/SQL:  SELECT * FROM authors LIMIT 0, 20
foreach ($authors as $author) {
	echo $author->name . '<br/>'."\r\n";
}

// list books with authors using JOIN (currently you have to use raw() function for JOINS)
$books = $booksCollection->raw('SELECT books.title, author.name
        FROM books
        LEFT JOIN authors AS author ON author._id == books.author_id');
foreach ($books as $book) {
	echo $book->{'books.title'} . ' (' . $book->{'author.name'} . ')<br/>'."\r\n";
}


// Another query builder example:
$results = $booksCollection->select(['name', 'color', 'price', 'category'])
	->where('color', 'red')
	->where('availability', true)
	->groupBy('category')
	->orderBy('price')
	->limit(5);

echo $results->getQuery()."<br/>\r\n";
