<?php
// https://www.clusterpoint.com/docs/api/4/php/386/quick-usage-example

// In this example we will use a simple database named "bookshelf" which consists of books and book authors.

// How to run this example?
// 1. Create Clusterpoint account.
// 2. Update $config variable with your account credentials
// 3. Create database "bookshelf"
// 4. Create collection under this db named "authors" (under Advanced choose "Enable hyper replication for this collection". This is for JOINs to work)
// 5. Create collection under this db named "books"
// 6. Include Clusterpoint Library
include 'vendor/autoload.php'; // if using Clusterpoint API with Composer
//include 'Clusterpoint.php'; // - without Composer

//Note, replace 'api-eu' with 'api-us', if you are using US Cloud server
$config = array(
	'host' => 'https://api-eu.clusterpoint.com/v4/',
	'account_id' => 'ACCOUNT_ID',
	'username' => 'USERNAME',
	'password' => 'PASSWORD',
	'debug' => false
);
$config = array(
	'host' => 'https://api-eu.clusterpoint.com/v4/',
	'account_id' => '70',
	'username' => 'toms.binde@gmail.com',
	'password' => 'qweqwe',
	'debug' => false
);

$cp = new Clusterpoint\Client($config);

// connect to database
/*$bookshelfDB = $cp->database('bookshelf');

// connect to collection using database connection
$authorsCollection = $bookshelfDB->collection('authors');

// or one can connect straight to the collection like this
$booksCollection = $cp->database('bookshelf')->collection('books');*/

// the select a collection to work with
$booksCollection = $cp->database('bookshelf.books');

// you can use $cp instance multiple times to access any collection
$authorsCollection = $cp->database('bookshelf.authors');


// try to remove documents from both collections just for the purpose of this example
$ids = [];
foreach ($response = $authorsCollection->limit(10000)->get() as $author) {
	$ids[] = $author->_id;
}
$authorsCollection->deleteMany($ids);
$ids = [];
foreach ($response = $booksCollection->limit(10000)->get() as $book) {
	$ids[] = $book->_id;
}
$booksCollection->deleteMany($ids);


// INSERT a bunch of book authors
$documents = [];
for ($x = 0; $x < 50; $x++) {
	$documents[] = [
		'_id' => $x,
		'name' => 'John ' . $x,
	];
}
$authorsCollection->insertMany($documents);

// INSERT a bunch of books
$documents = [];
for ($x = 0; $x < 50; $x++) {
	$documents[] = [
		'_id' => $x,
		'title' => 'Book ' . $x,
		'category' => 'Science',
		'author_id' => $x,
	];
}
$booksCollection->insertMany($documents);


// list five authors
$authors = $authorsCollection->limit(5)->get();
echo $authors->executedQuery() . "\r\n"; // JS/SQL:  SELECT * FROM authors LIMIT 0, 5
foreach ($authors as $author) {
	echo $author->name . '' . "\r\n";
}

// list five books with authors using JOIN (currently you have to use raw() function for JOINS)
$books = $booksCollection->raw('SELECT books.title, author.name
        FROM books
        LEFT JOIN authors AS author ON author._id == books.author_id
        LIMIT 5');
foreach ($books as $book) {
	echo $book->{'books.title'} . ' (' . $book->{'author.name'} . ')' . "\r\n";
}

// Another query builder example:
$results = $booksCollection->select(['name', 'color', 'price', 'category'])
	->where('color', 'red')
	->where('availability', true)
	->groupBy('category')
	->orderBy('price')
	->limit(5);