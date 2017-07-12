# Useful Docker Commands

### Build Neo4j Image Manually
```
cd infrastructure/neo4j
docker build -t neotest --build-arg wikilanguage=bar .
```

### Start Neo4j Container Manually
##### Start DB
```
docker run --publish=7474:7474 --publish=7687:7687 neotest
```
The web-interface will be reachable at localhost:7474.
##### Start in Shell
You can also start the Neo4j-Container in a shell. In this case, neo4j will not be started automatically (since the docker-entrypoint.sh is not executed).
```
docker run  --publish=7474:7474 --publish=7687:7687 -ti neotest /bin/bash
```

So you should start this entrypoint manually (you might have to add /var/lib/neo4j/ to some directories in line 104 and 110). The docker-entrypoint.sh-script first extends the neo4j-config (so only execute it once!) so that it will be accessable in the browser and then starts neo4j.:
```
./docker-entrypoint.sh
```

If you want to start/stop Neo4j, use:
```
/var/lib/neo4j/bin/neo4j console | start | stop
```

##### View Neo4j-Logs
```
tail -f /var/lib/neo4j/logs/neo4j.log
```

##### Get Size of Database
```
du -sh /var/lib/neo4j/data/databases/wikipediadbms
```

### Attach to running Neo4j Container
```
# check container-ID
docker ps -a
docker exec -i -t C0NT41N3R1D /bin/bash
```


### Getting Started (Manually)
##### Generally
* Install Git
* Install PHP/Apache or Node.js
* Install Neo4j
* Install ArrangoDB

```
git clone https://github.com/pascalherrmann/DHBW-GraphDB-Prototype
cd DHBW-GraphDB-Prototype
```

##### Neo4j
* Download Neo4j and start database
* create Wikipedia-Data: run Shell-script in infrastructure/graphipediamac.sh
* copy the created folder in Neo4j's data directory (User/Neo4j/)
* Start Neo4j and choose the database

##### ArrangoDB

##### PHP

##### Node.js (optionally)
* if your Neo4j-host/password is different from localhost/asdf, set the appropriate environment variables:

```
export ENV_NEO4J_HOST=localhost
export ENV_NEO4J_PW=neo
```


```
# choose Node.js-directory
cd nodejs 

# install NPM dependencies
npm install

# start nodejs backend
npm start
```

And go to: http://localhost:8080



# More
##### Remove untagged images
```
docker images -f "dangling=true" -q 
```