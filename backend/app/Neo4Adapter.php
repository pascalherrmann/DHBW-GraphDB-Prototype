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


class Neo4Adapter implements WikiDbAdapterInterface
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
            ->addConnection('bolt', 'bolt://'.$_ENV['USER_NEO'].':'.$_ENV['PWD_NEO'].'@'.$_ENV['HOST_NEO'])
            ->build();
    }

    public function shortestPath(string $start, string $end)
    {

        $query = "  MATCH (p0:Page {title:{start}}), (p1:Page {title:{ziel}}),
                    p = shortestPath((p0)-[*..40]->(p1))
                    RETURN p";

        $parameters = array('start' => $start, 'ziel' => $end);

        $response = new \StdClass();
        //$response->in = $start;
        $path = null;
        try {
            $result = ($this->client->run($query, $parameters));
            if ($result->size() == 0) {
                $response->status = "NO_PATH_FOUND";
            } else {
                $path = $result->getRecord()->value('p');
                $response->status = "SUCCESS";
                $response->path = array();
                foreach ($path->nodes() as $node) {
                    $response->path[] = $node->value('title');
                }
            }
        }catch (\RuntimeException $e) {
            $response->status = "ERROR";
            $response->message = $e->getMessage();
            $response->code = $e->getCode();

        }

        return json_encode($response, JSON_UNESCAPED_UNICODE);



    }



    public function autocomplete(string $teilwort)
    {
        $query = "start n = node(*) where n.title =~ {subString} return n.title ORDER BY n.count DESC LIMIT 10";

        $parameters= array('subString' => ".*".$teilwort.".*");

        $response = new \StdClass();
        try{
            $result = $this->client->run($query, $parameters);
            foreach ($result->records() as $record) {
                $response->pages[] = $record->get('n.title');
            }
            $response->status = "SUCCESS";

        }catch (\Exception $e) {
            $response->status = "ERROR";
            $response->message = $e->getMessage();
            $response->code = $e->getCode();
        }
        return json_encode($response, JSON_UNESCAPED_UNICODE);
    }

    public function randomEntry()
    {

        $query = "MATCH (n) WHERE rand() <= 0.0001  RETURN n.title AS random LIMIT 1";
        $response = new \StdClass();
        try{
            $response->entry = $this->client->run($query)->getRecord()->values()[0];
            $response->status = "SUCCESS";
        }catch (\Exception $e) {
            $response->status = "ERROR";
            $response->message = $e->getMessage();
            $response->code = $e->getCode();
        }

        return json_encode($response, JSON_UNESCAPED_UNICODE);






    }
}
