<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get('/', function () use ($app) {
    return "<h1>WikiBackend läuft :) </h1>". $app->version();
});

$app->get('path/{dbsys}/{start}/{ziel}', 'WikiController@getShortestPath');

$app->get('autocomplete/{dbsys}/{substring}', 'WikiController@getAutoComplete');

$app->get('random/{dbsys}', 'WikiController@getRandomEntry');

