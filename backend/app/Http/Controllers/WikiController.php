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


        # Abfrage eines zufälligen Eintrages durch das ausgewählte DB-System
        if ($dbsys == "neo") {
            $adapter = new Neo4Adapter();
            return $adapter->randomEntry();

        }

        if ($dbsys == "arango") {
            $adapter = new ArangoAdapter();
            return $adapter->randomEntry();
        }



    }


    /**
     * Funktion zur Konvertierung von Leer- und Sonderzeichen in den Eingaben
     *
     * @param string $string    Zu konvertierender String
     * @return string           Konvertierter String
     */
    protected function convert(string $string) {
        return (strtr($string, array(

            '%20'       => ' ',
            '%C3%A4'    => 'ä',
            '%C3%BC'    => 'ü',
            '%C3%B6'    => 'ö',
            '%C3%96'    => 'Ö',
            '%C3%9C'    => 'Ü',
            '%C3%84'    => 'Ä',
            '%C3%9F'    => 'ß',
            '%E2%80%99' => '’',
            '%2F'       => '/',

        )));
    }


}
