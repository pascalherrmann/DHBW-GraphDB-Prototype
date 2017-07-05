<?php

namespace App\Http\Controllers;

use App\ArangoAdapter;
use App\Neo4Adapter;

class WikiController extends Controller
{


    function getShortestPath($start, $ziel, $dbsys) {

        if ($dbsys == "neo") {
            $adapter = new Neo4Adapter();
            return $adapter->shortestPath($start, $ziel);

        }

        if ($dbsys = "arango") {
           $adapter = new ArangoAdapter();
           return $adapter->shortestPath($start, $ziel);
        }


    }

    function getAutoComplete($substring, $dbsys) {

        if ($dbsys == "neo") {
            $adapter = new Neo4Adapter();
            return $adapter->autocomplete($substring);

        }

        if ($dbsys = "arango") {
            $adapter = new ArangoAdapter();
            return $adapter->autocomplete($substring);
        }
    }

}
