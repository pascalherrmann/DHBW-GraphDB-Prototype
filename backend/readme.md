```
docker build -t phptest .

docker-compose up

#go to localhost:7474 and change neo4j-PW to neo

#checkout the port of php: $ docker ps -a
http://localhost:PORT/public/path/neo/Minga/Bier

```

#### Attach to shell:
````
docker exec -i -t CONTAINER /bin/bash
```