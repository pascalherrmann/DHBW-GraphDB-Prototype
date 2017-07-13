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
// Kürzester Pfad
$app->get('{dbsys}/path/{start}/{ziel}', 'WikiController@getShortestPath');

// Autovervollständigung
$app->get('{dbsys}/autocomplete/{substring}', 'WikiController@getAutoComplete');

// Zufallsseite
$app->get('{dbsys}/random', 'WikiController@getRandomEntry');

