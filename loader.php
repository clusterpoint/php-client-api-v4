<?php

foreach (array('Contracts', 'Exceptions', 'Helper', 'Standart') as $path) {
    foreach (glob(__DIR__."/src/{$path}/*.php") as $filename) {
        include_once $filename;
    }
}
include_once __DIR__.'/src/Response/Scope.php';
include_once __DIR__.'/src/Response/Response.php';
include_once __DIR__.'/src/Response/Batch.php';
include_once __DIR__.'/src/Response/Single.php';
include_once __DIR__.'/src/Transport/Rest.php';
include_once __DIR__.'/src/Query/Scope.php';
include_once __DIR__.'/src/Query/Parser.php';
include_once __DIR__.'/src/Query/Builder.php';
include_once __DIR__.'/src/Instance/Service.php';
include_once __DIR__.'/src/Client.php';