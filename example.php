<?php
// https://www.clusterpoint.com/docs/api/4/php/386/quick-usage-example

// In this example we will use a simple database named "bookshelf" which consists of books and book authors.

// How to run this example?
// 1. Create Clusterpoint account.
// 2. Update $config variable with your account credentials
// 3. Include Clusterpoint Library:
//include 'vendor/autoload.php'; // if using Clusterpoint API with Composer
include 'Clusterpoint.php'; // - without Composer

//Note, replace 'api-eu' with 'api-us', if you are using US Cloud server
$config = array(
	'host' => 'https://api-eu.clusterpoint.com/v4/',
	'account_id' => 'ACCOUNT_ID',
	'username' => 'USERNAME',
	'password' => 'PASSWORD',
	'debug' => false
);


// Create Clusterpoint connection
$clusterpoint = new Clusterpoint\Client($config);

try {
	$clusterpoint->dropDatabase('bookshelf');
} catch (Exception $e) {
}

// create database
$clusterpoint->createDatabase('bookshelf');

// connect to the newly created bookshelf database
$bookshelfDB = $clusterpoint->database('bookshelf');

// create collection with custom configuration
$cfg = [
//	'shards' => 3,
//	'replicas' => 3,
	'hyperreplication' => true,
//	'dataModel' => array(),
//	'config' => array(),
];
$bookshelfDB->createCollection('authors', $cfg);

// create another collection
$bookshelfDB->createCollection('books');

// select collections to work with
$booksCollection = $clusterpoint->database('bookshelf.books');
$authorsCollection = $clusterpoint->database('bookshelf.authors');

// make sure collectionas are initialized
$collectionsReady = false;
while (!$collectionsReady) {
	$response1 = $booksCollection->getStatus();
	$response2 = $authorsCollection->getStatus();
	if ($response1->collectionStatus() === '0' && $response2->collectionStatus() === '0') {
		$collectionsReady = true;
	}
}

// list collections in database
$response = $clusterpoint->listCollections('bookshelf');
foreach ($response as $data) {
	echo $data->name . "\r\n";;
}

// list all databases
$response = $clusterpoint->listDatabases();
foreach ($response as $data) {
	echo $data->name . "\r\n";;
}


// try to remove documents from both collections just for the purpose of this example
$ids = [];
foreach ($response = $authorsCollection->limit(10000)->get() as $author) {
	$ids[] = $author->_id;
}
if (count($ids) > 0) {
	$authorsCollection->deleteMany($ids);
}

$ids = [];
foreach ($response = $booksCollection->limit(10000)->get() as $book) {
	$ids[] = $book->_id;
}
if (count($ids) > 0) {
	$booksCollection->deleteMany($ids);
}


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
echo $authors->getQuery() . "\r\n"; // JS/SQL:  SELECT * FROM authors LIMIT 0, 5
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



// allocate a new transaction
$transaction = $booksCollection->transaction();

try {
	$response = $transaction->update('doc_id', 'price = price + 10');
	$transaction->commit();
} // if exception thrown
catch (Exception $e) {
	$msg = $e->getMessage();
	$transaction->rollback();
}


// edit collection configuration
$cfg = [
	'dataModel' => array(),
	'config' => array(),
];
$bookshelfDB->editCollection('books', $cfg);

//how to clear collections?
$booksCollection->clear();
$authorsCollection->clear();

//reindex
$cfg = [
//	'inBackground' => true,
//	'shard' => 1,
//	'node' => 5,
];
$booksCollection->reindex($cfg);

//describe
$response = $booksCollection->describe();

//drop collection
$bookshelfDB->dropCollection('books');

//drop database with all collections
$clusterpoint->dropDatabase('bookshelf');