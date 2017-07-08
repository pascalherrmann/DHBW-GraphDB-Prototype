# DHBW-GraphDB-Prototype

### Getting Started with Docker

##### PHP + Neo4j + Arangodb:
```
docker-compose up
```
* Go to: http://localhost:7474, log in with neo4j/neo4j and change password (e.g. neo).
* Try http://localhost/php-api/path/neo/Minga/Bier

##### Node.js + Neo4j:
```
docker-compose -f docker-compose-node.yaml up
```
* Go to: http://localhost:7474, log in with neo4j/neo4j and change password (e.g. neo).
* Restart again and go to http://localhost:8080.