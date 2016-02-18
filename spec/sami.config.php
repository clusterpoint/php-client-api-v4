<?php

use Sami\RemoteRepository\GitHubRemoteRepository;
use Sami\Sami;
use Sami\Version\GitVersionCollection;
use Symfony\Component\Finder\Finder;
$dir = '../src';

ini_set('memory_limit', -1);

$iterator = Finder::create()
        ->files()
        ->name('*.php')
        ->in($dir);
$versions = GitVersionCollection::create($dir)
    ->addFromTags('v4.*');

return new Sami($iterator, array(
    'title'                => 'Clusterpoint 4.0 PHP Client API',
    'versions'             => $versions,
    'build_dir'            => __DIR__.'/docapi/build/%version%',
    'cache_dir'            => __DIR__.'/docapi/cache/%version%',
    'default_opened_level' => 2,
    'remote_repository'    => new GitHubRemoteRepository('clusterpoint/php-client-api-v4', '../')
));