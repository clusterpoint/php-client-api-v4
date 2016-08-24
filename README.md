# Clusterpoint 4.x PHP Client API

[![License](https://poser.pugx.org/clusterpoint/php-client-api-v4/license)](https://packagist.org/packages/clusterpoint/php-client-api-v4)
[![Total Downloads](https://poser.pugx.org/clusterpoint/php-client-api-v4/downloads)](https://packagist.org/packages/clusterpoint/php-client-api-v4)
[![Latest Stable Version](https://poser.pugx.org/clusterpoint/php-client-api-v4/v/stable)](https://packagist.org/packages/clusterpoint/php-client-api-v4)
[![Latest Unstable Version](https://poser.pugx.org/clusterpoint/php-client-api-v4/v/unstable)](https://packagist.org/packages/clusterpoint/php-client-api-v4)

Clusterpoint is a NoSQL document database known for its innovative Cloud-based distributed architecture, fast processing speed, and a flexible "pay as you use" pricing model. The database also features a developer-friendly API suitable for many popular modern programming languages, including PHP -- the specific API which is the focus of this document. Its full support for ACID-compliant transactions is a rarity among NoSQL databases, making the product useful for situations where data integrity is a must.

The recently introduced fourth edition of Clusterpoint added a unique JavaScript/SQL query language with computational capabilities,, allowing you to create powerful queries to store, retrieve, and transform data. The PHP API is flexible enough to allow you to use either interface methods or raw JS/SQL queries to accomplish your database tasks. The decision to use either approach ultimately depends on programmer preference and the individual development scenario.

## Clusterpoint 4.x PHP Client API
* [Official Documentation](#documentation)
* [Requirements](#requirements)
* [Getting Started](#start)
* [Quick Example](#usage)
* [Bugs and Vulnerabilities](#bugs)
* [License](#license)

<a name="documentation"></a>
## Official Documentation

Documentation for the API can be found on the [Clusterpoint website](https://www.clusterpoint.com/docs/api/4/php/389).

<a name="requirements"></a>
## Requirements

	PHP >= 5.4.0
	cURL PHP Extension
	Composer

<a name="start"></a>
## Getting Started

1. **Sign up for Clusterpoint** – Before you begin, you need to
   sign up for a Clusterpoint account and retrieve your [Clusterpoint credentials](https://clusterpoint.com/docs/4.0/21/cloud-account-setup).
1. **Minimum requirements** – To run the PHP Client API, your system will need to meet the
   [minimum requirements](#requirements), including having **PHP >= 5.4.0
   compiled with the cURL extension and cURL 4.0.2+.
1. **Install the API** – [Composer](https://getcomposer.org/) is the right way to install the PHP Client API.  
``composer require clusterpoint/php-client-api-v4``
1. **Publish config file** - this is optional step, you can pass access points during workflow, but this might make your development process easier.  
``php -r "copy('vendor/clusterpoint/php-client-api-v4/src/config.example', 'clusterpoint.php');"``  
1. **Access** – You can pass the credentials inside on `Clusterpoint\Client` class construction, or use **clusterpoint.php** inside your project root folder, to manage your access points.  

<a name="usage"></a>
## Quick Example
```PHP
<?php
require 'vendor/autoload.php';
// or if you installed api without composer:
// require 'api_install_folder/Clusterpoint.php'

use Clusterpoint\Client;

//Initialize the service
$cp = new Client([
	'host' => 'https://api-eu.clusterpoint.com/v4',
	'account_id' => '1',
	'username' => 'root',
	'password' => 'password',
	'debug' => true,
]);

// Set the database.collection to initalize the query builder for it.
$bikes = $cp->database("shop.bikes");

// Build your query
$results = $bikes->where('color', 'red')
	->where('availability', true)
	->limit(5)
	->groupBy('category')
	->orderBy('price')
	->select(['name', 'color', 'price', 'category'])
	->get();

// Access your results
echo "First bike price: ".$results[0]->price;
```


<a name="bugs"></a>
## Support, Feature Requests & Bug Reports

* [GitHub issues](https://github.com/clusterpoint/php-client-api-v4/issues) for bug reports and feature requests
* [StackOverflow](https://stackoverflow.com) to ask questions (please make sure to use the [clusterpoint](http://stackoverflow.com/questions/tagged/clusterpoint) tag)
* You can also send an e-mail to our support team at support@clusterpoint.com

<a name="license"></a>
## License

Clusterpoint 4.x PHP Client API is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)