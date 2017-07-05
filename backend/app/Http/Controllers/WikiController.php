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

    function getRandomEntry($dbsys) {


        # Erstellen eines Objektes für die Response an das Angular Frontend
        $response = new \StdClass();

        # Abfrage eines zufälligen Eintrages durch das ausgewählte DB-System
        if ($dbsys == "neo") {
            $adapter = new Neo4Adapter();
            $response->entry = $adapter->randomEntry();

        }

        if ($dbsys == "arango") {
            $adapter = new ArangoAdapter();
            $response->entry = $adapter->randomEntry();
        }

        if (!(in_array($dbsys, ['neo', 'arango']))) {
            $response->status = "ERROR";
            $response->message = 'Kein korrektes Datenbanksystem ausgewählt! Wähle *neo* für Neo4J oder *arango* für ArangoDB';
        }

        # Ausgabe des Eintrages als korrektes JSON
        return json_encode($response, JSON_UNESCAPED_UNICODE);
    }

}
