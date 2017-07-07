<?php
/**
 * Created by PhpStorm.
 * User: hendrikpommerening
 * Date: 04.07.17
 * Time: 17:51
 */

namespace App;

use GraphAware\Neo4j\Client\ClientBuilder as ClientBuilder;
use Mockery\Exception;


class Neo4Adapter implements WikiInterface
{

    protected $client;

    /**
     * Neo4Adapter constructor.
     */
    public function __construct()
    {
        if (array_key_exists('USER_NEO', $_ENV) == false) $_ENV['USER_NEO']='neo4j';
        if (array_key_exists('PWD_NEO', $_ENV) == false) $_ENV['PWD_NEO']='neo';
        if (array_key_exists('HOST_NEO', $_ENV) == false) $_ENV['HOST_NEO']='localhost';
        if (array_key_exists('PORT_NEO', $_ENV) == false) $_ENV['PORT_NEO']='7687';

        $this->client = ClientBuilder::create()
            ->addConnection('bolt', 'bolt://'.$_ENV['USER_NEO'].':'.$_ENV['PWD_NEO'].'@'.$_ENV['HOST_NEO'].':'.$_ENV['PORT_NEO'])
            ->build();
    }

    public function shortestPath(string $start, string $end)
    {

        $query = "  MATCH (p0:Page {title:{start}}), (p1:Page {title:{ziel}}),
                    p = shortestPath((p0)-[*..7]->(p1))
                    RETURN p";

        $parameters = array('start' => $start, 'ziel' => $end);

        $response = new \StdClass();
        //$response->in = $start;
        $path = null;
        try {
            $path = ($this->client->run($query, $parameters)->getRecord()->value('p'));
            $response->status = "SUCCESS";
        } catch (\RuntimeException $e) {
            $response->status = "ERROR";
            $response->message = $e->getMessage();
            $response->code = $e->getCode();

            return json_encode($response, JSON_UNESCAPED_UNICODE);
        }


        $nodes = $path->nodes();

        foreach ($nodes as $node) {
            $steps[] = $node->value('title');
        }


        $response->nodes = $steps;


        return json_encode($response, JSON_UNESCAPED_UNICODE);


    }

    public function autocomplete(string $teilwort)
    {
        $query = "start n = node(*) where n.title =~ {subString} return n.title LIMIT 10";
    }

    public function randomEntry()
    {
        $client = self::getClient();


        $query = 'CREATE (database:Database {name:"Neo4j"})-[r:SAYS]->(message:Message {name:"Hello World!"}) RETURN database, message, r';

        $result = $client->run($query);
    }
}
