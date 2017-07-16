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

class ArangoAdapter implements WikiDbAdapterInterface
{

    protected $connection;

    public function __construct()
    {

        if (array_key_exists('USER_ARANGO', $_ENV) == false) $_ENV['USER_ARANGO'] = 'root';
        if (array_key_exists('PWD_ARANGO', $_ENV) == false) $_ENV['PWD_ARANGO'] = '';
        if (array_key_exists('HOST_ARANGO', $_ENV) == false) $_ENV['HOST_ARANGO'] = '127.0.0.1';
        if (array_key_exists('PORT_ARANGO', $_ENV) == false) $_ENV['PORT_ARANGO'] = '8529';


        $connectionOptions = array(
            // server endpoint to connect to
            ConnectionOptions::OPTION_ENDPOINT => 'tcp://' . $_ENV['HOST_ARANGO'] . ':' . $_ENV['PORT_ARANGO'],
            // authorization type to use (currently supported: 'Basic')
            ConnectionOptions::OPTION_AUTH_TYPE => 'Basic',
            // user for basic authorization
            ConnectionOptions::OPTION_AUTH_USER => $_ENV['USER_ARANGO'],
            // password for basic authorization
            ConnectionOptions::OPTION_AUTH_PASSWD => $_ENV['PWD_ARANGO'],
            // connection persistence on server. can use either 'Close' (one-time connections) or 'Keep-Alive' (re-used connections)
            ConnectionOptions::OPTION_CONNECTION => 'Close',
            // connect timeout in seconds
            ConnectionOptions::OPTION_TIMEOUT => 10,
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

        $query = "For page in pages 
    Filter page.name LIKE @name 
    SORT page.inEdgesCount DESC
    Limit 10
    return page.name";

        $statement = new Statement(
            $this->connection,
            array(
                "query" => $query,
                "count" => true,
                "batchSize" => 1000,
                "sanitize" => true,
                "bindVars" => array("name" => $teilwort . "%")
            )
        );
        $response = new \StdClass();

        # Statement ausführen und Ergebnis
        try {
            $cursor = $statement->execute();
            $result = ($cursor->getAll());
            $response->status = "SUCCESS";
            $response->pages = $result;

        } catch (\Exception $e) {
            $response->status = "ERROR";
            $response->pages = [];
            $response->code = $e->getCode();
            $response->message = $e->getMessage();
        }


        return json_encode($response, JSON_UNESCAPED_UNICODE);


    }

    public function shortestPath(string $start, string $end)
    {
        # ID zum Namen ermitteln: Hamburg -> pages/35d7df6ed3d93be2927d14acc5f1fc9a
        $start_id = "pages/".hash('sha256', $start);
        $ziel_id = "pages/".hash('sha256', $end);


        # AQL Query zur Ermittlung des Pfades:
        $query = 'FOR v IN OUTBOUND SHORTEST_PATH @startId TO @targetId GRAPH @graphName RETURN v.name';

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

        $response = new  \StdClass();
        try {
            # Statement ausführen und Ergebnis
            $timing['start'] = microtime(true);
            $cursor = $statement->execute();
            $timing['finish'] = microtime(true);
             if($cursor->getCount() == 0) {
                 $response->status = "NO_PATH_FOUND";
             } else {
                 $response->status = "SUCCESS";
                 $response->path = $cursor->getAll();
             }
            $response->execTime =  ($timing['finish'] - $timing['start']) * 1000;
        }catch (\Exception $e) {
            $response->status = "ERROR";
            $response->message = $e->getMessage();
            $response->code = $e->getCode();

        }




        return json_encode($response, JSON_UNESCAPED_UNICODE);


    }

    public function randomEntry()
    {


        # AQL Query zur Selektion eines zufälligen Eintrages
        $query = "FOR node IN @@Collection SORT RAND() LIMIT 1 RETURN node.name";

        # Statement zur Ausführung der Query erzeugen
        $statement = new Statement(
            $this->connection,
            array(
                "query" => $query,
                "count" => true,
                "batchSize" => 1000,
                "sanitize" => true,
                "bindVars" => array("@Collection" => "pages")
            )
        );

        $response = new \StdClasS();
        try{
            $cursor = $statement->execute();
            $response->status = "SUCCESS";
            $response->entry = $cursor->getAll()[0];

        }catch (\Exception $e){
            $response->status = "ERROR";
            $response->message = $e->getMessage();
            $response->code = $e->getCode();

        }
        # Statement ausführen und Ergebnis

        return json_encode($response, JSON_UNESCAPED_UNICODE);


    }



}
