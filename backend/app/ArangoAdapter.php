<?php
/**
 * Created by PhpStorm.
 * User: hendrikpommerening
 * Date: 04.07.17
 * Time: 17:49
 */

namespace App;

use triagens\ArangoDb\ConnectionOptions as ConnectionOptions;
use triagens\ArangoDb\UpdatePolicy as UpdatePolicy;
use triagens\ArangoDb\Connection as Connection;
use triagens\ArangoDb\Statement as Statement;

class ArangoAdapter implements WikiInterface
{

    protected $connection;

    /**
     * ArangoAdapter constructor.
     */
    public function __construct()
    {
        $connectionOptions = array(
            // server endpoint to connect to
            ConnectionOptions::OPTION_ENDPOINT => 'tcp://'.$_ENV['HOST_ARANGO'].':'.$_ENV['PORT_ARANGO'],
            // authorization type to use (currently supported: 'Basic')
            ConnectionOptions::OPTION_AUTH_TYPE => 'Basic',
            // user for basic authorization
            ConnectionOptions::OPTION_AUTH_USER => $_ENV['USER_ARANGO'],
            // password for basic authorization
            ConnectionOptions::OPTION_AUTH_PASSWD => $_ENV['PWD_ARANGO'],
            // connection persistence on server. can use either 'Close' (one-time connections) or 'Keep-Alive' (re-used connections)
            ConnectionOptions::OPTION_CONNECTION => 'Close',
            // connect timeout in seconds
            ConnectionOptions::OPTION_TIMEOUT => 3,
            // whether or not to reconnect when a keep-alive connection has timed out on server
            ConnectionOptions::OPTION_RECONNECT => true,
            // optionally create new collections when inserting documents
            ConnectionOptions::OPTION_CREATE => true,
            // optionally create new collections when inserting documents
            ConnectionOptions::OPTION_UPDATE_POLICY => UpdatePolicy::LAST,
        );

        $this->connection = new Connection($connectionOptions);
    }

    public function autocomplete(string $teilwort)
    {

        $query = "For doc in pages
    Filter doc.`name`LIKE @name
    Limit 25
    Return doc.`name`";
    }

    public function shortestPath(string $start, string $end)
    {

        # ID zum Namen ermitteln: Hamburg -> pages/35d7df6ed3d93be2927d14acc5f1fc9a
        $start_id = self::findID($start);
        $ziel_id = self::findID($end);

        # AQL Query zur Ermittlung des Pfades:
        $query = 'LET p = ( FOR v, e IN OUTBOUND SHORTEST_PATH @startId TO @targetId GRAPH @graphName
                            RETURN {vertex: v, edge: e, weight: (IS_NULL(e) ? 0 : 1)}
                          )
                FILTER LENGTH(p) > 0 
                RETURN { 
                        vertices: p[*].vertex,
                        edges: p[* FILTER CURRENT.e != null].edge,
                        distance: SUM(p[*].weight)
                       }';

        # Statement zur Ausführung der Query erzeugen
        $statement = new Statement(
            $this->connection,
            array(
                "query" => $query,
                "count" => true,
                "batchSize" => 1000,
                "sanitize" => true,
                "bindVars" => array("startId" => $start_id, "targetId" => $ziel_id, "graphName" => "graphipedia")
            )
        );

        # Statement ausführen und Ergebnis
        $cursor = $statement->execute();
        $result = ($cursor->getAll()[0]);


        echo(json_encode($result->vertices, JSON_PRETTY_PRINT));
        echo $result->distance;


    }

    public function randomEntry()
    {


        # AQL Query zur Selektion eines zufälligen Eintrages
        $query = "FOR doc IN @@collection
                  SORT RAND()
                  LIMIT 1
                   RETURN doc.`name`";

        # Statement zur Ausführung der Query erzeugen
        $statement = new Statement(
            $this->connection,
            array(
                "query" => $query,
                "count" => true,
                "batchSize" => 1000,
                "sanitize" => true,
                "bindVars" => array("@collection" => "pages")
            )
        );

        # Statement ausführen und Ergebnis
        $cursor = $statement->execute();
        return ($cursor->getAll()[0]);



    }


    protected function findID($name)
    {


        $query = "  FOR doc IN @@collection
                    FILTER doc.`name` == @name
                    RETURN doc.`_id`";

        # Statement zur Ausführung der Query erzeugen
        $statement = new Statement(
            $this->connection,
            array(
                "query" => $query,
                "count" => true,
                "batchSize" => 1000,
                "sanitize" => true,
                "bindVars" => array("@collection" => "pages", "name" => $name)
            )
        );

        # Statement ausführen und Ergebnis
        $cursor = $statement->execute();
        return ($cursor->getAll()[0]);



    }



}
