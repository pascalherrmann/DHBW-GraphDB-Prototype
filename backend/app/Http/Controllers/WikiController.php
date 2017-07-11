<?php

namespace App\Http\Controllers;

use App\ArangoAdapter;
use App\Neo4Adapter;

class WikiController extends Controller
{


    function getShortestPath($start, $ziel, $dbsys) {

        $start = self::convert($start);
        $ziel = self::convert($ziel);

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

        $substring = self::convert($substring);

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
            $response = $adapter->randomEntry();

        }

        if ($dbsys == "arango") {
            $adapter = new ArangoAdapter();
            $response = $adapter->randomEntry();
        }

        if (!(in_array($dbsys, ['neo', 'arango']))) {
            $response->status = "ERROR";
            $response->message = 'Kein korrektes Datenbanksystem ausgewählt! Wähle *neo* für Neo4J oder *arango* für ArangoDB';
        }

        # Ausgabe des Eintrages als korrektes JSON
        return json_encode($response, JSON_UNESCAPED_UNICODE);
    }


    /**
     * Funktion zur Konvertierung von Leer- und Sonderzeichen in den Eingaben
     *
     * @param string $string    Zu konvertierender String
     * @return string           Konvertierter String
     */
    protected function convert(string $string) {
        return strtr($string, array(

            '%20'       => ' ',
            '%C3%A4'    => 'ä',
            '%C3%BC'    => 'ü',
            '%C3%B6'    => 'ö',
            '%C3%96'    => 'Ö',
            '%C3%9C'    => 'Ü',
            '%C3%84'    => 'Ä',
            '%C3%9F'    => 'ß',
            '%E2%80%99' => '’'

        ));
    }


}
