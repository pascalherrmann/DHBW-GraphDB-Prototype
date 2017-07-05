<?php
/**
 * Created by PhpStorm.
 * User: hendrikpommerening
 * Date: 04.07.17
 * Time: 17:51
 */

namespace App;

use GraphAware\Neo4j\Client\ClientBuilder as ClientBuilder;


class Neo4Adapter implements WikiInterface
{

    public function shortestPath(string $start, string $end)
    {
        // TODO: Implement shortestPath() method.
    }

    public function autocomplete(string $teilwort)
    {
        // TODO: Implement autocomplete() method.
    }

    public function randomEntry()
    {
       $client = self::getClient();

       $query = "";

       $result = $client->run($query);

    }

    private function getClient() {
        $client = ClientBuilder::create()
            ->addConnection('bolt', 'bolt://neo4j:neo4j@localhost:7687')
            ->build();
        return $client;
    }

}
