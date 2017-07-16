# DHBW-GraphDB-Prototype

This app was created as a prototype for our DHBW-project "concepts and operational scenarios of graph databases" where we analyzed and evaluated different graph databases.

Searching for an interesting use case with a very huge amount of publically available data, we discovered the *Wikipedia-Game*: We downloaded the wikipedia-dumps and parsed them using the graphipedia-script to import the Wikipedia-Pages (Nodes) and links between them (Edges) in our test winning graph databases, Neo4j and ArangoDB. Using our app, you can calculate the shortest path between to Wikipedia-pages, demonstrating the power of graph databases.

Have fun!

### Authors
* @pascalherrmann: Front-End (AngularJS), Node.js, Neo4j, Docker-Images
* @henp95: Back-End (PHP), ArangoDB, Data import with Python

### Getting Started
We used Docker to simplifiy the installation process. All you have to do in order to build and start the required Docker images is the following:

##### PHP + Neo4j + Arangodb:
```
docker-compose up
```
* Go To http://localhost:8081

##### Node.js + Neo4j:
```
docker-compose -f docker-compose-node.yaml up
```
* Go to http://localhost:8080

You might wanna apply some changes to `docker-compose.yaml` in order to change port allocation or language of the installed wikipedia.

### Screenshot


![Optional Text](../master/public/img/screen2.png)
