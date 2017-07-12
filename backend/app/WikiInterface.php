<?php
/**
 * Created by PhpStorm.
 * User: hendrikpommerening
 * Date: 04.07.17
 * Time: 17:36
 */

namespace App;


interface WikiInterface
{

    /**
     * Funktion zur Ausgabe einer Liste mit möglichen Einträgen in der Datenbank
     * Limiert auf 10 Treffer
     *
     * @param   string $teilwort       Anfang des zu suchenenden Wortes
     *
     * @return  string                 JSON-Response mit Status und Liste möglicher Wörter
     */
    public function autocomplete(string $teilwort) ;

    /**
     * Funktion zur Ermittlung des kürzesten Pfades zwischen zwei Einträgen
     *
     * @param   string $start       Name der Startseite
     * @param   string  $end        Name der Zielseite
     *
     * @return  string              JSON Mit: Liste der nötigen Schritte
     *                              Ausführungszeit
     *                              Verwendete Datenbank
     */
    public function shortestPath(string $start, string $end);


    /**
     * Funktion zur Ausgabe eines zufälligen Eintrags
     *
     * @return string
     */
    public function randomEntry();


}
