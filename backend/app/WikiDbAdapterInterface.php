<?php
namespace App;

interface WikiDbAdapterInterface
{
    /**
     * Funktion zur Ausgabe einer Liste mit 10 möglichen Einträgen
     *
     * @param   string $teilwort       Anfang des zu suchenenden Wortes
     * @return  string                 JSON-Response
     */
    public function autocomplete(string $teilwort) ;

    /**
     * Funktion zur Ermittlung des kürzesten Pfades zwischen zwei Einträgen
     *
     * @param   string $start      Name der Startseite
     * @param   string $end        Name der Zielseite
     * @return  string             JSON-Response
     */
    public function shortestPath(string $start, string $end);

    /**
     * Funktion zur Ausgabe eines zufälligen Eintrags
     *
     * @return string       JSON-Response
     */
    public function randomEntry();
}
